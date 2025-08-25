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
        // 1. Drop the old pivot table if it exists
        Schema::dropIfExists('card_deck');

        // 2. Add the new 'deck_id' column to the 'cards' table
        Schema::table('cards', function (Blueprint $table) {
            $table->foreignId('deck_id')
                  ->after('user_id') 
                  ->constrained()
                  ->onDelete('cascade'); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the pivot table (for rollback safety)
        Schema::create('card_deck', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->foreignId('deck_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Remove the 'deck_id' column from the 'cards' table
        Schema::table('cards', function (Blueprint $table) {
            // It's good practice to drop the foreign key before the column
            $table->dropForeign(['deck_id']);
            $table->dropColumn('deck_id');
        });
    }
};
