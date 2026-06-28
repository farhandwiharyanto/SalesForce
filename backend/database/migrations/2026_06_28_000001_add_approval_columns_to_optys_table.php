<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opty', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable()->after('customer_id');
            $table->string('contract_document_path')->nullable()->after('contract_id');
            
            // Approval statuses: pending, approved, rejected, none
            $table->string('pimpinan_approval_status')->default('none')->after('discount_amount');
            $table->string('director_approval_status')->default('none')->after('pimpinan_approval_status');
            $table->string('verificator_approval_status')->default('none')->after('director_approval_status');
            
            $table->text('rejection_note')->nullable()->after('verificator_approval_status');
            
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('opty', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropColumn([
                'contract_id',
                'contract_document_path',
                'pimpinan_approval_status',
                'director_approval_status',
                'verificator_approval_status',
                'rejection_note'
            ]);
        });
    }
};
