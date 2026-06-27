<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sia')->unique()->nullable();
            $table->string('nomor_customer')->unique();
            $table->string('customer_name');
            $table->enum('status', ['Registered', 'Active', 'Deactivated'])->default('Registered');
            $table->string('email')->unique();
            $table->string('initial', 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
