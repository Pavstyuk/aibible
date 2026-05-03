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
        // id, translation, verse_id, ai_comment, date

        Schema::create('ai_comments', function (Blueprint $table) {
            $table->engine('InnoDB');
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->id();
            $table->tinyText('translation');
            $table->integer('verse_id')->unsigned();
            $table->text('ai_comment');
            $table->timestamp('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
