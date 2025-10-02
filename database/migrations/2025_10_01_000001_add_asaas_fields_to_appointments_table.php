<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'clinic_id')) {
                $table->unsignedBigInteger('clinic_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('appointments', 'status')) {
                $table->string('status', 30)->default('payment_pending')->after('clinic_id');
            }
            if (!Schema::hasColumn('appointments', 'availability_day_option')) {
                $table->string('availability_day_option')->nullable()->after('status');
            }
            if (!Schema::hasColumn('appointments', 'availability_time_window')) {
                $table->string('availability_time_window')->nullable()->after('availability_day_option');
            }
            if (!Schema::hasColumn('appointments', 'asaas_charge_id')) {
                $table->string('asaas_charge_id')->nullable()->after('availability_time_window');
            }
            if (!Schema::hasColumn('appointments', 'asaas_pix_qr_code')) {
                $table->longText('asaas_pix_qr_code')->nullable()->after('asaas_charge_id');
            }
            if (!Schema::hasColumn('appointments', 'asaas_pix_payload')) {
                $table->longText('asaas_pix_payload')->nullable()->after('asaas_pix_qr_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'clinic_id','status','availability_day_option',
                'availability_time_window','asaas_charge_id',
                'asaas_pix_qr_code','asaas_pix_payload'
            ]);
        });
    }
};
