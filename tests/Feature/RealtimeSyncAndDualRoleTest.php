<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\Puskesmas;
use App\Models\Tapos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeSyncAndDualRoleTest extends TestCase
{
    use RefreshDatabase;

    private Puskesmas $puskesmas;
    private Tapos $tapos;
    private User $admin;
    private User $kader;
    private Balita $balita;
    private IbuHamil $ibuHamil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->puskesmas = Puskesmas::create([
            'nama' => 'Puskesmas Sukamaju',
            'alamat' => 'Jl. Merdeka No. 1',
            'kecamatan' => 'Sukamaju',
            'kabupaten' => 'Kota Sehat',
        ]);

        $this->tapos = Tapos::create([
            'puskesmas_id' => $this->puskesmas->id,
            'nama' => 'Tapos Melati',
            'kode' => 'TAP-001',
            'alamat' => 'Jl. Melati RW 01',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Sukamaju',
            'status' => 'aktif',
        ]);

        $this->admin = User::create([
            'name' => 'Admin Puskesmas',
            'email' => 'admin@posyandusmart.test',
            'password' => 'password123',
            'role' => 'admin',
            'puskesmas_id' => $this->puskesmas->id,
        ]);

        $this->kader = User::create([
            'name' => 'Kader Melati 1',
            'email' => 'kader1@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $this->puskesmas->id,
            'tapos_id' => $this->tapos->id,
        ]);

        $this->balita = Balita::create([
            'tapos_id' => $this->tapos->id,
            'nama' => 'Balita Uji',
            'nik' => '3201010101010001',
            'nama_ibu' => 'Ibu Uji',
            'tanggal_lahir' => Carbon::now()->subMonths(12),
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Melati No. 5',
            'rt_rw' => '01/01',
            'berat_badan_lahir' => 3.2,
            'tinggi_badan_lahir' => 49.0,
            'status' => 'aktif',
        ]);

        $this->ibuHamil = IbuHamil::create([
            'tapos_id' => $this->tapos->id,
            'nama' => 'Ibu Hamil Uji',
            'nik' => '3201010101010002',
            'tanggal_lahir' => Carbon::now()->subYears(28),
            'nama_suami' => 'Suami Uji',
            'hpht' => Carbon::now()->subMonths(4),
            'taksiran_persalinan' => Carbon::now()->addMonths(5),
            'alamat' => 'Jl. Melati No. 5',
            'rt_rw' => '01/01',
            'status' => 'aktif',
        ]);
    }

    public function test_realtime_sync_endpoint_returns_json_stats(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('api.realtime.sync'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'server_time',
            'latest_db_update',
            'has_updates',
            'admin_stats' => [
                'total_tapos',
                'total_balita',
                'total_ibu_hamil',
                'balita_stunting_count',
                'pending_validation_count',
                'monitoring_tapos',
            ],
            'recent_balita_exams',
            'recent_bumil_exams',
        ]);
    }

    public function test_realtime_sync_detects_new_examinations(): void
    {
        $pastTimestamp = Carbon::now()->subMinutes(5)->timestamp;

        // Kader creates new examination
        $exam = PemeriksaanBalita::create([
            'balita_id' => $this->balita->id,
            'tanggal_pemeriksaan' => Carbon::now(),
            'umur_bulan' => 12,
            'berat_badan' => 7.2,
            'tinggi_badan' => 68.0,
            'lingkar_kepala' => 44.0,
            'status_stunting' => 'risiko_stunting',
            'hasil_ai' => 'risiko_stunting',
            'status_validasi' => 'pending',
            'kehadiran' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('api.realtime.sync', [
            'last_sync' => $pastTimestamp,
        ]));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertTrue($data['has_updates']);
        $this->assertEquals(1, $data['admin_stats']['pending_validation_count']);
        $this->assertEquals(1, $data['admin_stats']['balita_stunting_count']);
        $this->assertNotEmpty($data['notifications']);
    }

    public function test_dual_role_session_switching_works(): void
    {
        // Switch to Kader
        $response = $this->get(route('switch.role', 'kader'));
        $response->assertRedirect(route('kader.dashboard'));

        $this->assertEquals('kader', auth()->user()->role);

        // Switch to Admin
        $response = $this->get(route('switch.role', 'admin'));
        $response->assertRedirect(route('admin.dashboard'));

        $this->assertEquals('admin', auth()->user()->role);
    }
}
