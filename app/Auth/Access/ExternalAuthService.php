<?php

namespace App\Auth\Access;

use App\Auth\Role;
use App\Auth\User;
use Illuminate\Database\Eloquent\Builder;

class ExternalAuthService
{
    /**
     * Check a role against an array of group names to see if it matches.
     * Checked against role 'external_auth_id' if set otherwise the name of the role.
     */
    protected function roleMatchesGroupNames(Role $role, array $groupNames): bool
    {
        if ($role->external_auth_id) {
            return $this->externalIdMatchesGroupNames($role->external_auth_id, $groupNames);
        }

        $roleName = str_replace(' ', '-', trim(strtolower($role->display_name)));
        return in_array($roleName, $groupNames);
    }

    /**
     * Check if the given external auth ID string matches one of the given group names.
     */
    protected function externalIdMatchesGroupNames(string $externalId, array $groupNames): bool
    {
        $externalAuthIds = explode(',', strtolower($externalId));

        foreach ($externalAuthIds as $externalAuthId) {
            if (in_array(trim($externalAuthId), $groupNames)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Match an array of group names to App system roles.
     * Formats group names to be lower-case and hyphenated.
     * @param array $groupNames
     * @return \Illuminate\Support\Collection
     */
    protected function matchGroupsToSystemsRoles(array $groupNames)
    {
        foreach ($groupNames as $i => $groupName) {
            $groupNames[$i] = str_replace(' ', '-', trim(strtolower($groupName)));
        }

        $roles = Role::query()->where(function (Builder $query) use ($groupNames) {
            $query->whereIn('name', $groupNames);
            foreach ($groupNames as $groupName) {
                $query->orWhere('external_auth_id', 'LIKE', '%' . $groupName . '%');
            }
        })->get();

        $matchedRoles = $roles->filter(function (Role $role) use ($groupNames) {
            return $this->roleMatchesGroupNames($role, $groupNames);
        });

        return $matchedRoles->pluck('id');
    }

    /**
     * Sync the groups to the user roles for the current user
     * @param \App\Auth\User $user
     * @param array $userGroups
     */
    public function syncWithGroups(User $user, array $userGroups)
    {
        // Get the ids for the roles from the names
        $groupsAsRoles = $this->matchGroupsToSystemsRoles($userGroups);

        // Sync groups
        if ($this->config['remove_from_groups']) {
            $user->roles()->sync($groupsAsRoles);
            $this->userRepo->attachDefaultRole($user);
        } else {
            $user->roles()->syncWithoutDetaching($groupsAsRoles);
        }
    }
}
