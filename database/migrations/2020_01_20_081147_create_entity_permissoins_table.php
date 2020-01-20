<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityPermissoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_permissoins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('restrictable_id');
            $table->string('restrictable_type');
            $table->integer('role_id');
            $table->string('action');
            $table->index('role_id');
            $table->index('action');
            $table->index(['restrictable_id', 'restrictable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_permissoins');
    }
}
