<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddViewPermissionsToRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('role', function (Blueprint $table) {
            $currentRoles = DB::table('roles')->get();

            // Create new view permission
            $entities = ['Book', 'Page', 'Chapter'];
            $ops = ['View All', 'View Own'];
            foreach ($entities as $entity) {
                foreach ($ops as $op) {
                    $permId = DB::table('role_permissions')->insertGetId([
                    'name' => strtolower($entity) . '-' . strtolower(str_replace(' ', '-', $op)),
                    'display_name' => $op . ' ' . $entity . 's',
                    'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                    'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                    // Assign view permission to all current roles
                    foreach ($currentRoles as $role) {
                        DB::table('permission_role')->insert([
                        'role_id' => $role->id,
                        'permission_id' => $permId
                    ]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role', function (Blueprint $table) {
            //
        });
    }
}
