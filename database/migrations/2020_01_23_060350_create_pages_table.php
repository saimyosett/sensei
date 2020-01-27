<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id');
            $table->integer('chapter_id');
            $table->string('name');
            $table->string('slug')->indexed();
            $table->longText('markdown');
            $table->longText('html');
            $table->longText('text');
            $table->integer('priority');
            $table->integer('revision_count');
            $table->boolean('draft')->default(false);
            $table->boolean('template')->default(false);
            $table->boolean('restricted')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            $table->index('slug');
            $table->index('book_id');
            $table->index('chapter_id');
            $table->index('priority');
            $table->index('draft');
            $table->index('template');
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
        Schema::dropIfExists('pages');
    }
}
