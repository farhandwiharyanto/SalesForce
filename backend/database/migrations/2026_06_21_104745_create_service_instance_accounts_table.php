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
        Schema::create('service_instance_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('sia_number')->unique();
            $table->foreignId('deal_id')->constrained('opty')->cascadeOnDelete();
            $table->string('customer_id'); // We'll store formatted string like "123"
            $table->string('company_name');
            $table->string('billing_account_number')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('status')->default('Registered');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_instance_accounts');
    }
};
