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
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['name', 'phone', 'company']);
            
            $table->string('lead_number')->unique()->after('id')->nullable();
            $table->string('first_name')->after('lead_number')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
            
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            // Status stays, we might want to just make sure it exists
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['product_id']);
            
            $table->dropColumn(['lead_number', 'first_name', 'last_name', 'customer_id', 'owner_id', 'product_id']);
        });
    }
};
