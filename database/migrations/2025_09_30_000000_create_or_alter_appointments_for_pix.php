<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('specialty_id')->constrained()->restrictOnDelete();
                $table->unsignedBigInteger('clinic_id')->nullable();
                $table->string('cep', 9);
                $table->string('city');
                $table->string('state', 2);
                $table->string('available_days');     // any|mon_wed_fri|tue_thu
                $table->string('preferred_time');     // 08-18|08-13|13-18
                $table->string('indication')->nullable();
                $table->text('notes')->nullable();
                $table->decimal('coparticipation_price', 10, 2);
                $table->enum('status', ['awaiting_payment','confirmed','completed','canceled'])->default('awaiting_payment');
                $table->string('asaas_payment_id')->nullable();
                $table->string('asaas_invoice_url')->nullable();
                $table->text('asaas_pix_qr_base64')->nullable();
                $table->text('asaas_pix_payload')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('appointments', function (Blueprint $table) {
                if (!Schema::hasColumn('appointments','clinic_id')) $table->unsignedBigInteger('clinic_id')->nullable();
                if (!Schema::hasColumn('appointments','available_days')) $table->string('available_days')->default('any');
                if (!Schema::hasColumn('appointments','preferred_time')) $table->string('preferred_time')->default('08-18');
                if (!Schema::hasColumn('appointments','coparticipation_price')) $table->decimal('coparticipation_price',10,2)->default(0);
                if (!Schema::hasColumn('appointments','status')) $table->enum('status', ['awaiting_payment','confirmed','completed','canceled'])->default('awaiting_payment');
                if (!Schema::hasColumn('appointments','asaas_payment_id')) $table->string('asaas_payment_id')->nullable();
                if (!Schema::hasColumn('appointments','asaas_invoice_url')) $table->string('asaas_invoice_url')->nullable();
                if (!Schema::hasColumn('appointments','asaas_pix_qr_base64')) $table->text('asaas_pix_qr_base64')->nullable();
                if (!Schema::hasColumn('appointments','asaas_pix_payload')) $table->text('asaas_pix_payload')->nullable();
            });
        }
    }
    public function down(): void {
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                foreach (['clinic_id','available_days','preferred_time','coparticipation_price','status','asaas_payment_id','asaas_invoice_url','asaas_pix_qr_base64','asaas_pix_payload'] as $c) {
                    if (Schema::hasColumn('appointments',$c)) $table->dropColumn($c);
                }
            });
        }
    }
};