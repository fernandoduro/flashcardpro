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
        Schema::table('cards', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('cards', 'difficulty')) {
                $table->dropColumn('difficulty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            // Re-add the column for rollback safety
            if (! Schema::hasColumn('cards', 'difficulty')) {
                $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium')->after('answer');
            }
        });
    }
};
