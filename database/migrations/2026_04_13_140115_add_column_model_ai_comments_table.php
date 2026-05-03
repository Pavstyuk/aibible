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
        Schema::table('ai_comments', function (Blueprint $table) {
            // Add your new column here
            $table->string('model')->after('ai_comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_comments', function (Blueprint $table) {
            // Add your new column here
            $table->dropColumn('model');
        });
    }
};
