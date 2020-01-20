<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('image_id')->nullable()->default(null);
            $table->string('slug')->indexed();
            $table->text('description');
            $table->boolean('restricted')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->index('slug');
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
        Schema::dropIfExists('books');
    }
}
