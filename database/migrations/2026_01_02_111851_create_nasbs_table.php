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
        Schema::create('nasb', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->id();
            $table->tinyText('book_name');
            $table->tinyInteger('book_num')->unsigned();
            $table->tinyInteger('chapter_num')->unsigned();
            $table->tinyInteger('verse_num')->unsigned();
            $table->text('verse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasbs');
    }
};