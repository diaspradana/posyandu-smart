<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tapos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('puskesmas_id')
                ->constrained('puskesmas')
                ->cascadeOnDelete();

            $table->string('nama');
            $table->string('kode')->unique();

            $table->text('alamat')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();

            $table->string('nama_ketua')->nullable();
            $table->string('no_hp')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->enum('status', [
                'aktif',
                'tidak_aktif'
            ])->default('aktif');

            $table->timestamps();

            $table->index(['puskesmas_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tapos');
    }
};