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
        Schema::table('service_instance_accounts', function (Blueprint $table) {
            $table->string('billing_account_number')->nullable()->after('company_name');
            // Since contract table might not exist yet, we just add it as a nullable foreign ID or an integer for now
            $table->unsignedBigInteger('contract_id')->nullable()->after('billing_account_number');
        });
        
        // Update default status
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_instance_accounts ALTER COLUMN status SET DEFAULT 'Registered'");
        // Update existing rows
        \Illuminate\Support\Facades\DB::table('service_instance_accounts')->where('status', 'generated')->update(['status' => 'Registered']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_instance_accounts', function (Blueprint $table) {
            $table->dropColumn(['billing_account_number', 'contract_id']);
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE service_instance_accounts ALTER COLUMN status SET DEFAULT 'generated'");
    }
};
