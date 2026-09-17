<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->cascadeOnDelete();
            $table->date('tanggal_pemeriksaan');
            $table->decimal('berat_badan', 5, 2);
            $table->decimal('tinggi_badan', 5, 2);
            $table->enum('status_stunting', ['normal', 'pemantauan', 'risiko_stunting', 'stunting'])->default('normal');
            $table->enum('status_imunisasi', ['lengkap', 'belum_lengkap', 'tertunda'])->default('lengkap');
            $table->boolean('kehadiran')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_balita');
    }
};
