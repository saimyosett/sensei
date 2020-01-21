<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('extra')->nullable();
            $table->integer('book_id')->indexed();
            $table->integer('user_id');
            $table->integer('entity_id');
            $table->string('entity_type');
            $table->nullableTimestamps();

            $table->index('book_id');
            $table->index('user_id');
            $table->index('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
