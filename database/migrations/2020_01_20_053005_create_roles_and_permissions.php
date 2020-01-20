<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Much of this code has been taken from entrust,
 * a role & permission management solution for Laravel.
 *
 * Full attribution of the database Schema shown below goes to the entrust project.
 *
 * @license MIT
 * @package Zizaco\Entrust
 * @url https://github.com/Zizaco/entrust
 */

class CreateRolesAndPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create table for storing roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('system_name');
            $table->string('description')->nullable();
            $table->nullableTimestamps();

            $table->index('system_name');
        });

        // Create table for associating roles to users (Many-to-Many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['user_id', 'role_id']);
        });

        // Create table for storing permissions
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->nullableTimestamps();
        });

        // Create table for associating permissions to roles (Many-to-Many)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')->references('id')->on('role_permissions')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // Create default roles
        $adminId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'display_name' => 'Admin',
            'system_name' => 'admin',
            'description' => 'Administrator of the whole application',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        $editorId = DB::table('roles')->insertGetId([
            'name' => 'editor',
            'display_name' => 'Editor',
            'system_name' => 'editor',
            'description' => 'User can edit Books, Chapters & Pages',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        $viewerId = DB::table('roles')->insertGetId([
            'name' => 'viewer',
            'display_name' => 'Viewer',
            'system_name' => 'viewer',
            'description' => 'User can view books & their content behind authentication',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()
        ]);

        // Get roles with permissions we need to change
        $adminRoleId = DB::table('roles')->where('name', '=', 'admin')->first()->id;
        $editorRole = DB::table('roles')->where('name', '=', 'editor')->first();

        // Create & attach new entity permissions
        $entities = ['Book', 'Page', 'Chapter', 'Image'];
        $ops = ['Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        foreach ($entities as $entity) {
            foreach ($ops as $op) {
                $permissionId = DB::table('role_permissions')->insertGetId([
                    'name' => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                DB::table('permission_role')->insert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId
                ]);
                if ($editorRole !== null) {
                    DB::table('permission_role')->insert([
                        'role_id' => $editorRole->id,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }

        // Create & attach new admin permissions
        $permissionsToCreate = [
            'settings-manage' => 'Manage Settings',
            'users-manage' => 'Manage Users',
            'user-roles-manage' => 'Manage Roles & Permissions',
            'restrictions-manage-all' => 'Manage All Entity Permissions',
            'restrictions-manage-own' => 'Manage Entity Permissions On Own Content',
            'templates-manage' => 'Manage Page Templates'
        ];
        foreach ($permissionsToCreate as $name => $displayName) {
            $permissionId = DB::table('role_permissions')->insertGetId([
                'name' => $name,
                'display_name' => $displayName,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
            ]);
            DB::table('permission_role')->insert([
                'role_id' => $adminRoleId,
                'permission_id' => $permissionId
            ]);
        }

        // Set all current users as admins
        // (At this point only the initially create user should be an admin)
        $users = DB::table('users')->get()->all();
        foreach ($users as $user) {
            DB::table('role_user')->insert([
                'role_id' => $adminId,
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('role_permissions');
        Schema::drop('permission_role');
        Schema::drop('role_user');
        Schema::drop('roles');
    }
}
