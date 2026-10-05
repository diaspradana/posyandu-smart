<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tapos;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LaporanParameterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_admin_laporan_default_view()
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get(route('admin.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Eksekutif Kesehatan Wilayah');
        $response->assertSee('Parameter Laporan Wilayah');
        $response->assertSee('Rekapitulasi Pelayanan per Posyandu Tapos');
    }

    public function test_admin_laporan_filter_tapos_and_periode()
    {
        $admin = User::where('role', 'admin')->first();
        $tapos = Tapos::first();

        $response = $this->actingAs($admin)->get(route('admin.laporan', [
            'tapos_id' => $tapos->id,
            'periode' => 'Agustus 2026',
            'jenis' => 'semua',
        ]));

        $response->assertStatus(200);
        $response->assertSee($tapos->nama);
        $response->assertSee('Agustus 2026');
    }

    public function test_admin_laporan_filter_jenis_balita_with_status_risiko()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'balita',
            'status_filter' => 'risiko',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pemeriksaan Balita');
        $response->assertSee('Risiko Stunting');
    }

    public function test_admin_laporan_filter_jenis_ibu_hamil_with_validasi_pending()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'ibu_hamil',
            'status_validasi' => 'pending',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pemeriksaan Ibu Hamil');
    }

    public function test_admin_laporan_search_filter()
    {
        $admin = User::where('role', 'admin')->first();
        $sampleBalita = Balita::first();

        $response = $this->actingAs($admin)->get(route('admin.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'balita',
            'search' => $sampleBalita->nama,
        ]));

        $response->assertStatus(200);
        $response->assertSee($sampleBalita->nama);
    }

    public function test_admin_laporan_yearly_period()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.laporan', [
            'periode' => 'Tahun 2026',
            'jenis' => 'semua',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tahun 2026');
    }

    public function test_kader_laporan_default_view()
    {
        $kader = User::where('role', 'kader')->first();
        $response = $this->actingAs($kader)->get(route('kader.laporan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Kegiatan');
        $response->assertSee('Parameter Laporan Posyandu');
    }

    public function test_kader_laporan_filter_balita()
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('kader.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'balita',
            'status_filter' => 'normal',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pemeriksaan Balita');
    }

    public function test_kader_laporan_filter_ibu_hamil()
    {
        $kader = User::where('role', 'kader')->first();

        $response = $this->actingAs($kader)->get(route('kader.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'ibu_hamil',
            'status_filter' => 'risiko',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daftar Pemeriksaan Ibu Hamil');
    }

    public function test_kader_laporan_search()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos() ?? Tapos::first();
        $sampleBalita = Balita::where('tapos_id', $tapos->id)->first();

        $response = $this->actingAs($kader)->get(route('kader.laporan', [
            'periode' => 'Agustus 2026',
            'jenis' => 'balita',
            'search' => $sampleBalita->nama,
        ]));

        $response->assertStatus(200);
        $response->assertSee($sampleBalita->nama);
    }
}
