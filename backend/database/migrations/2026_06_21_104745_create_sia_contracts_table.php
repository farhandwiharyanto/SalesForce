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
        Schema::create('sia_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('sia_number')->unique();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->string('customer_id'); // We'll store formatted string like "123"
            $table->string('company_name');
            $table->string('status')->default('generated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sia_contracts');
    }
};
