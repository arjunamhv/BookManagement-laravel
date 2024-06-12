<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->string('isbn', 13)->unique()->nullable(false);
            $table->string('title', 100)->nullable(false);
            $table->string('subtitle', 255)->nullable(true);
            $table->string('author', 100)->nullable(false);
            $table->string('published', 100)->nullable(true);
            $table->string('publisher', 100)->nullable(false);
            $table->integer('pages')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('website', 100)->nullable(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
