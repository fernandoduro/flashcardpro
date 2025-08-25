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
        Schema::table('decks', function (Blueprint $table) {
            // Drop the column if it exists
            if (Schema::hasColumn('decks', 'time_per_card')) {
                $table->dropColumn('time_per_card');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('decks', function (Blueprint $table) {
            // Re-add the column if it doesn't exist, to make the migration reversible
            if (!Schema::hasColumn('decks', 'time_per_card')) {
                $table->unsignedInteger('time_per_card')->nullable()->comment('in seconds')->after('name');
            }
        });
    }
};