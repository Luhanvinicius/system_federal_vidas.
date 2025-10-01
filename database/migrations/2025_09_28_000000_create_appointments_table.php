<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnUpdate()->restrictOnDelete();
                $table->foreignId('clinic_id')->nullable()->constrained('clinics')->cascadeOnUpdate()->restrictOnDelete();

                $table->string('cep', 9);
                $table->string('city', 120);
                $table->string('state', 2);

                $table->string('status', 20)->default('pending');
                $table->string('indication', 255)->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
