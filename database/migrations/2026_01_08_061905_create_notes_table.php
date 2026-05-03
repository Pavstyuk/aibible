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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unsigned();
            $table->unsignedInteger('verse_id')->unsigned();
            $table->char('translation_name', 4);
            $table->string('title');
            $table->string('content');
            $table->string('slug');
            $table->string('seo_title');
            $table->string('seo_description');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('likes')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
