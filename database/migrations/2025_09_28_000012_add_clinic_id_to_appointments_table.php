<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('appointments','clinic_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('clinic_id')->after('specialty_id')->constrained('clinics')->cascadeOnUpdate()->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('appointments','clinic_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropConstrainedForeignId('clinic_id');
            });
        }
    }
};
