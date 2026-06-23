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
        Schema::rename('sia_contracts', 'service_instance_accounts');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_instance_accounts', function (Blueprint $table) {
            //
        });
    }
};
