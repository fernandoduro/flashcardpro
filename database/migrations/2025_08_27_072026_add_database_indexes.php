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
        // Add indexes to decks table for better query performance
        Schema::table('decks', function (Blueprint $table) {
            // Index for dashboard queries (user decks ordered by pinned status)
            $table->index(['user_id', 'is_pinned'], 'decks_user_pinned_index');

            // Index for public deck queries
            $table->index('public', 'decks_public_index');

            // Index for deck creation date queries
            $table->index(['user_id', 'created_at'], 'decks_user_created_index');
        });

        // Add indexes to cards table for better query performance
        Schema::table('cards', function (Blueprint $table) {
            // Index for deck-card relationships
            $table->index('deck_id', 'cards_deck_index');

            // Index for user ownership queries
            $table->index('user_id', 'cards_user_index');

            // Composite index for user's cards in a deck
            $table->index(['user_id', 'deck_id'], 'cards_user_deck_index');
        });

        // Add indexes to studies table for better query performance
        Schema::table('studies', function (Blueprint $table) {
            // Index for user study statistics
            $table->index(['user_id', 'completed_at'], 'studies_user_completed_index');

            // Index for deck study statistics
            $table->index('deck_id', 'studies_deck_index');

            // Index for finding recent studies
            $table->index(['user_id', 'created_at'], 'studies_user_created_index');
        });

        // Add indexes to study_results table for better query performance
        Schema::table('study_results', function (Blueprint $table) {
            // Index for study result queries
            $table->index(['study_id', 'is_correct'], 'study_results_study_correct_index');

            // Index for card performance analysis
            $table->index('card_id', 'study_results_card_index');

            // Composite index for user performance queries
            $table->index(['card_id', 'is_correct'], 'study_results_card_correct_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from study_results table
        Schema::table('study_results', function (Blueprint $table) {
            $table->dropIndex('study_results_study_correct_index');
            $table->dropIndex('study_results_card_index');
            $table->dropIndex('study_results_card_correct_index');
        });

        // Remove indexes from studies table
        Schema::table('studies', function (Blueprint $table) {
            $table->dropIndex('studies_user_completed_index');
            $table->dropIndex('studies_deck_index');
            $table->dropIndex('studies_user_created_index');
        });

        // Remove indexes from cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex('cards_deck_index');
            $table->dropIndex('cards_user_index');
            $table->dropIndex('cards_user_deck_index');
        });

        // Remove indexes from decks table
        Schema::table('decks', function (Blueprint $table) {
            $table->dropIndex('decks_user_pinned_index');
            $table->dropIndex('decks_public_index');
            $table->dropIndex('decks_user_created_index');
        });
    }
};
