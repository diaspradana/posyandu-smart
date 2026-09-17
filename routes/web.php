<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\TaposController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\IbuHamilController;
use App\Http\Controllers\KaderController;
use App\Http\Controllers\Admin\JadwalAdminController;
use App\Http\Controllers\Admin\ValidasiPemeriksaanController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.process');
});

Route::post('/logout', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard (Role-based redirect or default index)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin Puskesmas
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'admin'
        ])->name('dashboard');

        Route::get('/monitoring', [
            DashboardController::class,
            'monitoring'
        ])->name('monitoring');

        Route::get('/monitoring/balita', [
            DashboardController::class,
            'monitoringBalita'
        ])->name('monitoring.balita');

        Route::get('/monitoring/ibu-hamil', [
            DashboardController::class,
            'monitoringIbuHamil'
        ])->name('monitoring.ibu-hamil');

        // Validasi Pemeriksaan oleh Admin Puskesmas
        Route::prefix('validasi')->name('validasi.')->group(function () {
            Route::get('/', [ValidasiPemeriksaanController::class, 'index'])->name('index');
            Route::post('/balita/{pemeriksaan}/approve', [ValidasiPemeriksaanController::class, 'validasiBalita'])->name('balita.approve');
            Route::post('/balita/{pemeriksaan}/reject', [ValidasiPemeriksaanController::class, 'tolakBalita'])->name('balita.reject');
            Route::post('/ibu-hamil/{pemeriksaan}/approve', [ValidasiPemeriksaanController::class, 'validasiIbuHamil'])->name('ibu-hamil.approve');
            Route::post('/ibu-hamil/{pemeriksaan}/reject', [ValidasiPemeriksaanController::class, 'tolakIbuHamil'])->name('ibu-hamil.reject');
        });

        // Kegiatan Posyandu (Jadwal & Kehadiran)
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('/', [JadwalAdminController::class, 'index'])->name('index');
            Route::post('/', [JadwalAdminController::class, 'store'])->name('store');
            Route::put('/{jadwal}', [JadwalAdminController::class, 'update'])->name('update');
            Route::delete('/{jadwal}', [JadwalAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{jadwal}/setujui-perubahan', [JadwalAdminController::class, 'setujuiPerubahan'])->name('setujui-perubahan');
            Route::post('/{jadwal}/tolak-perubahan', [JadwalAdminController::class, 'tolakPerubahan'])->name('tolak-perubahan');
        });

        Route::get('/kehadiran', [
            DashboardController::class,
            'kehadiran'
        ])->name('kehadiran');

        Route::get('/laporan', [
            DashboardController::class,
            'laporan'
        ])->name('laporan');

        Route::get('/pengaturan', [
            DashboardController::class,
            'pengaturan'
        ])->name('pengaturan');

        // Pemeriksaan (Lihat Riwayat & Detail)
        Route::get('/pemeriksaan/balita', [
            PemeriksaanController::class,
            'balita'
        ])->name('pemeriksaan.balita');

        Route::get('/pemeriksaan/ibu-hamil', [
            PemeriksaanController::class,
            'ibuHamil'
        ])->name('pemeriksaan.ibu-hamil');

        // API Endpoint for dynamic Attendance Chart filtering
        Route::get('/api/chart-kehadiran', [
            DashboardController::class,
            'chartKehadiran'
        ])->name('chart.kehadiran');

    });

/*
|--------------------------------------------------------------------------
| Kader Posyandu
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:kader'])
    ->prefix('kader')
    ->name('kader.')
    ->group(function () {

        // 1. Dashboard Kader
        Route::get('/dashboard', [
            KaderController::class,
            'dashboard'
        ])->name('dashboard');

        // 2. Data Warga (Master Data Kader)
        Route::get('/warga/balita', [
            KaderController::class,
            'wargaBalita'
        ])->name('warga.balita');

        Route::get('/warga/ibu-hamil', [
            KaderController::class,
            'wargaIbuHamil'
        ])->name('warga.ibu-hamil');

        // 3. Pemeriksaan
        Route::get('/pemeriksaan', [
            KaderController::class,
            'pemeriksaan'
        ])->name('kegiatan.pemeriksaan');

        Route::post('/pemeriksaan', [
            KaderController::class,
            'storePemeriksaan'
        ])->name('kegiatan.pemeriksaan.store');

        // 4. Monitoring (Balita & Ibu Hamil)
        Route::get('/monitoring/balita', [
            KaderController::class,
            'monitoringStunting'
        ])->name('monitoring.balita');

        Route::get('/monitoring/ibu-hamil', [
            KaderController::class,
            'monitoringIbuHamil'
        ])->name('monitoring.ibu-hamil');

        Route::get('/monitoring-stunting', [
            KaderController::class,
            'monitoringStunting'
        ])->name('monitoring-stunting');

        Route::get('/ai-detail/balita/{balita}', [
            KaderController::class,
            'aiDetailBalita'
        ])->name('ai-detail.balita');

        Route::get('/ai-detail/ibu-hamil/{ibuHamil}', [
            KaderController::class,
            'aiDetailIbuHamil'
        ])->name('ai-detail.ibu-hamil');

        Route::get('/ai-detail/{balita}', [
            KaderController::class,
            'aiDetailBalita'
        ])->name('ai-detail');

        // 5. Kegiatan Posyandu (Jadwal & Kehadiran)
        Route::get('/kegiatan/jadwal', [
            KaderController::class,
            'jadwal'
        ])->name('kegiatan.jadwal');

        Route::post('/kegiatan/jadwal/{jadwal}/konfirmasi', [
            KaderController::class,
            'konfirmasiKesiapan'
        ])->name('kegiatan.jadwal.konfirmasi');

        Route::post('/kegiatan/jadwal/{jadwal}/ajukan-perubahan', [
            KaderController::class,
            'ajukanPerubahanJadwal'
        ])->name('kegiatan.jadwal.ajukan-perubahan');

        Route::get('/kegiatan/kehadiran', [
            KaderController::class,
            'kehadiran'
        ])->name('kegiatan.kehadiran');

        // 6. Laporan
        Route::get('/laporan', [
            KaderController::class,
            'laporan'
        ])->name('laporan');

        // 7. Profil
        Route::get('/profil', [
            KaderController::class,
            'profil'
        ])->name('profil');

        Route::put('/profil', [
            KaderController::class,
            'updateProfil'
        ])->name('profil.update');

    });

/*
|--------------------------------------------------------------------------
| Master Data (CRUD Shared)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,kader'])
    ->group(function () {

        Route::resource('tapos', TaposController::class)->parameters([
            'tapos' => 'tapo'
        ]);

        Route::resource('balita', BalitaController::class)->parameters([
            'balita' => 'balita'
        ]);

        Route::resource(
            'ibu-hamil',
            IbuHamilController::class
        )->parameters([
            'ibu-hamil' => 'ibuHamil'
        ]);

    });