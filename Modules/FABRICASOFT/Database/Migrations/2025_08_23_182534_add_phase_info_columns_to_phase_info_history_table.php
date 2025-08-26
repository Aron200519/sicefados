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
        Schema::table('fabricasoft_phase_info_history', function (Blueprint $table) {
            // Add new columns for storing complete phase information
            $table->text('description')->nullable()->after('content');
            $table->text('notes')->nullable()->after('description');
            $table->string('external_link')->nullable()->after('notes');
            $table->date('start_date')->nullable()->after('external_link');
            $table->date('end_date')->nullable()->after('start_date');
            $table->text('additional_comment')->nullable()->after('end_date');
            
            // Make content column nullable since we'll use the new specific columns
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fabricasoft_phase_info_history', function (Blueprint $table) {
            // Remove the new columns
            $table->dropColumn([
                'description',
                'notes', 
                'external_link',
                'start_date',
                'end_date',
                'additional_comment'
            ]);
            
            // Revert content column to not nullable
            $table->text('content')->nullable(false)->change();
        });
    }
};
