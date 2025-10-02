<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'availability_days')) {
                $table->string('availability_days')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'best_time')) {
                $table->string('best_time')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'clinic_id')) {
                $table->unsignedBigInteger('clinic_id')->nullable()->index();
            }
            if (!Schema::hasColumn('appointments', 'status')) {
                $table->string('status')->default('pending_payment')->index();
            }
            if (!Schema::hasColumn('appointments', 'asaas_charge_id')) {
                $table->string('asaas_charge_id')->nullable()->index();
            }
            if (!Schema::hasColumn('appointments', 'asaas_invoice_url')) {
                $table->string('asaas_invoice_url')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'asaas_qr_code_url')) {
                $table->text('asaas_qr_code_url')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'asaas_qr_code_payload')) {
                $table->text('asaas_qr_code_payload')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            foreach (['availability_days','best_time','clinic_id','status','asaas_charge_id','asaas_invoice_url','asaas_qr_code_url','asaas_qr_code_payload'] as $col) {
                if (Schema::hasColumn('appointments', $col)) $table->dropColumn($col);
            }
        });
    }
};
