<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            return; // será criada pela migration create_appointments_table
        }

        if (!Schema::hasColumn('appointments', 'clinic_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (Schema::hasColumn('appointments', 'specialty_id')) {
                    $table->foreignId('clinic_id')->after('specialty_id')->constrained('clinics')->cascadeOnUpdate()->restrictOnDelete();
                } else {
                    $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnUpdate()->restrictOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('appointments') && Schema::hasColumn('appointments', 'clinic_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('clinic_id');
            });
        }
    }
};
