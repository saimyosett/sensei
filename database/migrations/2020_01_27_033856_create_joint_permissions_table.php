<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJointPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('joint_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('action');
            $table->boolean('has_permission')->default(false);
            $table->boolean('has_permission_own')->default(false);
            $table->integer('created_by');
            // Create indexes
            $table->index(['entity_id', 'entity_type']);
            $table->index('has_permission');
            $table->index('has_permission_own');
            $table->index('role_id');
            $table->index('action');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('joint_permissions');
    }
}
