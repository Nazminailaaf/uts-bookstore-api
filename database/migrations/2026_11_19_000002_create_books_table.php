<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('author_id');
            $table->string('title', 150);
            $table->string('category', 50);
            $table->string('isbn', 17)->unique();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('published_year');
            $table->timestamps();

            // relasi: authors 1 --- N books
            $table->foreign('author_id')
                  ->references('id')->on('authors')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
}
