<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->indexed();
            $table->string('name');
            $table->longText('html');
            $table->longText('text');
            $table->string('slug');
            $table->string('book_slug');
            $table->string('summary')->nullable();
            $table->string('type')->default('version');
            $table->longText('markdown')->default('');
            $table->integer('revision_number');
            $table->integer('created_by');
            $table->timestamps();

            $table->index('slug');
            $table->index('book_slug');
            $table->index('type');
            $table->index('revision_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_revisions');
    }
}
