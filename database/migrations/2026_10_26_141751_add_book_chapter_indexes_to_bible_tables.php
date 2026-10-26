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
        foreach (['rbo', 'rst', 'nasb'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['book_num', 'chapter_num']);
            });
        }
    }

    public function down(): void
    {
        foreach (['rbo', 'rst', 'nasb'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['book_num', 'chapter_num']);
            });
        }
    }
};
