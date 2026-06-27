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
        Schema::create('opty', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_number')->unique()->nullable();
            $table->string('name');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->decimal('estimated_value_otc', 15, 2);
            $table->decimal('estimated_value_mrc', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
            $table->enum('discount_status', ['none', 'pending', 'approved', 'rejected'])->default('none');
            $table->date('target_close_date')->nullable();
            $table->date('customer_expected_rfs')->nullable();
            $table->enum('request_type', [
                'New Installation',
                'Addon',
                'Change Service',
                'Change Plan',
                'Relocation'
            ])->default('New Installation');
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
            $table->boolean('is_converted_from_lead')->default(false);
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opty');
    }
};
