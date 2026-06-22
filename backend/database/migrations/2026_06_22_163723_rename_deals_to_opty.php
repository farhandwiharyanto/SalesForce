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
        // First drop the foreign key in sia_contracts
        Schema::table('sia_contracts', function (Blueprint $table) {
            $table->dropForeign(['deal_id']);
            $table->renameColumn('deal_id', 'opty_id');
        });

        // Rename the deals table to opty
        Schema::rename('deals', 'opty');

        // Add back the foreign key to opty
        Schema::table('sia_contracts', function (Blueprint $table) {
            $table->foreign('opty_id')->references('id')->on('opty')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new foreign key
        Schema::table('sia_contracts', function (Blueprint $table) {
            $table->dropForeign(['opty_id']);
            $table->renameColumn('opty_id', 'deal_id');
        });

        // Rename the opty table back to deals
        Schema::rename('opty', 'deals');

        // Add back the old foreign key
        Schema::table('sia_contracts', function (Blueprint $table) {
            $table->foreign('deal_id')->references('id')->on('deals')->cascadeOnDelete();
        });
    }
};
