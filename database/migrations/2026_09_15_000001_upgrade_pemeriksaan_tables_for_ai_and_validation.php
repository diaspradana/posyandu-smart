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
        Schema::table('pemeriksaan_ibu_hamil', function (Blueprint $table) {
            $table->integer('systolic_bp')->nullable()->after('tekanan_darah');
            $table->integer('diastolic_bp')->nullable()->after('systolic_bp');
            $table->decimal('blood_sugar', 5, 2)->nullable()->after('diastolic_bp'); // mmol/L or mg/dL converted
            $table->decimal('body_temp', 5, 2)->nullable()->after('blood_sugar'); // °F / °C
            $table->integer('heart_rate')->nullable()->after('body_temp');
            $table->string('ai_risk_level', 20)->nullable()->after('heart_rate'); // low, medium, high
            $table->decimal('ai_probability', 5, 4)->nullable()->after('ai_risk_level');
            $table->json('ai_probabilities')->nullable()->after('ai_probability');
            $table->string('ai_model_version', 50)->nullable()->default('maternal-v1.0')->after('ai_probabilities');
            $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->default('pending')->after('status_pemeriksaan');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->text('catatan')->nullable()->after('catatan_validasi');
        });

        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->integer('umur_bulan')->nullable()->after('tanggal_pemeriksaan');
            $table->decimal('lingkar_kepala', 5, 2)->nullable()->after('tinggi_badan');
            $table->string('hasil_ai', 30)->nullable()->after('status_stunting'); // normal, pemantauan, risiko_stunting
            $table->decimal('ai_probability', 5, 4)->nullable()->after('hasil_ai');
            $table->json('ai_probabilities')->nullable()->after('ai_probability');
            $table->string('ai_model_version', 50)->nullable()->default('stunting-v1.0')->after('ai_probabilities');
            $table->enum('status_pemeriksaan', ['diperiksa', 'belum_diperiksa'])->default('diperiksa')->after('kehadiran');
            $table->enum('status_validasi', ['pending', 'validated', 'rejected'])->default('pending')->after('status_pemeriksaan');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->text('catatan')->nullable()->after('catatan_validasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_ibu_hamil', function (Blueprint $table) {
            $table->dropColumn([
                'systolic_bp',
                'diastolic_bp',
                'blood_sugar',
                'body_temp',
                'heart_rate',
                'ai_risk_level',
                'ai_probability',
                'ai_probabilities',
                'ai_model_version',
                'status_validasi',
                'catatan_validasi',
                'catatan',
            ]);
        });

        Schema::table('pemeriksaan_balita', function (Blueprint $table) {
            $table->dropColumn([
                'umur_bulan',
                'lingkar_kepala',
                'hasil_ai',
                'ai_probability',
                'ai_probabilities',
                'ai_model_version',
                'status_pemeriksaan',
                'status_validasi',
                'catatan_validasi',
                'catatan',
            ]);
        });
    }
};
