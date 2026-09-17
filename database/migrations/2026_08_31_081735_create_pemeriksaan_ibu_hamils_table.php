<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_hamil_id')->constrained('ibu_hamil')->cascadeOnDelete();
            $table->date('tanggal_pemeriksaan');
            $table->decimal('berat_badan', 5, 2);
            $table->string('tekanan_darah', 20); // e.g. "120/80"
            $table->integer('usia_kehamilan_minggu');
            $table->enum('status_pemeriksaan', ['diperiksa', 'belum_diperiksa'])->default('diperiksa');
            $table->boolean('kehadiran')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_ibu_hamil');
    }
};
