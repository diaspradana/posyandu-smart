<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kader'])
                ->default('kader')
                ->after('email');

            $table->foreignId('puskesmas_id')
                ->nullable()
                ->after('role')
                ->constrained('puskesmas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn(['role', 'puskesmas_id']);
        });
    }
};