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
        // First drop old foreign keys
        Schema::table('opty', function (Blueprint $table) {
            $table->dropForeign('deals_contact_id_foreign');
            $table->dropColumn('contact_id');
            // product_id could be kept if it means Product Interest, the user didn't mention it for Opty but we can keep it.
            // Let's modify existing columns
        });

        Schema::table('opty', function (Blueprint $table) {
            $table->string('opportunity_number')->unique()->after('id')->nullable();
            
            $table->foreignId('assigned_to')->nullable()->after('name')->constrained('users')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->after('assigned_to')->constrained('users')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->after('owner_id')->constrained('customers')->onDelete('cascade');
            
            $table->date('target_close_date')->nullable();
            $table->date('customer_expected_rfs')->nullable();
            
            $table->enum('request_type', [
                'New Installation',
                'Addon',
                'Change Service',
                'Change Plan',
                'Relocation'
            ])->default('New Installation')->after('target_close_date');
            
            // Drop old stage column and recreate
            $table->dropColumn('stage');
        });

        Schema::table('opty', function (Blueprint $table) {
            $table->enum('stage', [
                'Prospect and Analysis',
                'Value Proposition',
                'Perception Analysis',
                'Proposal or Quote',
                'Negotiation or Review',
                'Closed Won',
                'Closed Lost'
            ])->default('Prospect and Analysis');

            $table->enum('confidence_level', [
                'PipeLine',
                'Base Case',
                'Commit'
            ])->default('PipeLine');

            // Amount field is already there, rename it or add new
            $table->renameColumn('amount', 'estimated_value_otc');
            $table->decimal('estimated_value_mrc', 15, 2)->nullable()->after('amount');
            
            $table->boolean('is_converted_from_lead')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opty', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['customer_id']);
            
            $table->dropColumn([
                'opportunity_number',
                'assigned_to',
                'owner_id',
                'customer_id',
                'target_close_date',
                'customer_expected_rfs',
                'request_type',
                'stage',
                'confidence_level',
                'estimated_value_mrc',
                'is_converted_from_lead'
            ]);
            
            $table->renameColumn('estimated_value_otc', 'amount');
            $table->string('stage')->default('prospecting');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('cascade');
        });
    }
};
