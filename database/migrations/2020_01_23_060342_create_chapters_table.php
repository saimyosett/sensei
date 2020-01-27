<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id');
            $table->string('slug')->indexed();
            $table->text('name');
            $table->text('description');
            $table->integer('priority');
            $table->boolean('restricted')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->index('slug');
            $table->index('book_id');
            $table->index('priority');
            $table->index('restricted');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapters');
    }
}
