<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balita', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tapos_id')
                ->constrained('tapos')
                ->cascadeOnDelete();

            $table->string('nik', 16)->unique();

            $table->string('nama');
            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ]);

            $table->date('tanggal_lahir');

            $table->string('nama_ibu')->nullable();
            $table->string('nama_ayah')->nullable();

            $table->string('no_hp_orang_tua')->nullable();

            $table->enum('status', [
                'aktif',
                'pindah',
                'meninggal'
            ])->default('aktif');

            $table->timestamps();

            $table->index([
                'tapos_id',
                'status'
            ]);

            $table->index('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balita');
    }
};