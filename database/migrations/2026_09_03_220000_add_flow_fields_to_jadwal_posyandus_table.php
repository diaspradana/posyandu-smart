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
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->string('jenis_kegiatan')->nullable()->after('nama_kegiatan');
            $table->string('lokasi')->nullable()->after('waktu_selesai');
            $table->text('catatan')->nullable()->after('lokasi');
            $table->enum('status_konfirmasi', [
                'menunggu_konfirmasi',
                'siap',
                'usulan_perubahan',
                'ditolak'
            ])->default('menunggu_konfirmasi')->after('status');
            $table->text('alasan_perubahan')->nullable()->after('status_konfirmasi');
            $table->date('usulan_tanggal')->nullable()->after('alasan_perubahan');
            $table->time('usulan_waktu_mulai')->nullable()->after('usulan_tanggal');
            $table->time('usulan_waktu_selesai')->nullable()->after('usulan_waktu_mulai');
            $table->string('usulan_lokasi')->nullable()->after('usulan_waktu_selesai');
            $table->text('alasan_penolakan')->nullable()->after('usulan_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_posyandu', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kegiatan',
                'lokasi',
                'catatan',
                'status_konfirmasi',
                'alasan_perubahan',
                'usulan_tanggal',
                'usulan_waktu_mulai',
                'usulan_waktu_selesai',
                'usulan_lokasi',
                'alasan_penolakan'
            ]);
        });
    }
};
