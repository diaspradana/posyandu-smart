<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tapos;
use App\Models\Balita;
use App\Models\IbuHamil;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KaderMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_kader_can_view_balita_master_data_list()
    {
        $kader = User::where('role', 'kader')->first();
        $response = $this->actingAs($kader)->get(route('kader.warga.balita'));

        $response->assertStatus(200);
        $response->assertSee('Data Balita');
        $response->assertSee('Tambah Balita Baru');
        $response->assertSee('Detail');
        $response->assertSee('Edit');
        $response->assertSee('Hapus');
    }

    public function test_kader_can_view_ibu_hamil_master_data_list()
    {
        $kader = User::where('role', 'kader')->first();
        $response = $this->actingAs($kader)->get(route('kader.warga.ibu-hamil'));

        $response->assertStatus(200);
        $response->assertSee('Data Ibu Hamil');
        $response->assertSee('Tambah Ibu Hamil Baru');
        $response->assertSee('Detail');
        $response->assertSee('Edit');
        $response->assertSee('Hapus');
    }

    public function test_kader_can_create_balita()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();

        // 1. Visit create page
        $createResponse = $this->actingAs($kader)->get(route('balita.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Tambah Data Balita');

        // 2. Submit new balita
        $nik = '3201991234560001';
        $postResponse = $this->actingAs($kader)->post(route('balita.store'), [
            'tapos_id' => $tapos->id,
            'nik' => $nik,
            'nama' => 'Ananda Bintang',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '2024-05-10',
            'nama_ibu' => 'Ibu Rahayu',
            'nama_ayah' => 'Bpk. Surya',
            'no_hp_orang_tua' => '081299887766',
            'status' => 'aktif',
        ]);

        $postResponse->assertRedirect(route('kader.warga.balita'));
        $this->assertDatabaseHas('balita', [
            'nik' => $nik,
            'nama' => 'Ananda Bintang',
            'tapos_id' => $tapos->id,
        ]);
    }

    public function test_kader_can_edit_and_update_balita()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();
        $balita = Balita::where('tapos_id', $tapos->id)->first();

        // 1. Visit edit page
        $editResponse = $this->actingAs($kader)->get(route('balita.edit', $balita));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Data Balita');

        // 2. Update balita
        $updateResponse = $this->actingAs($kader)->put(route('balita.update', $balita), [
            'tapos_id' => $tapos->id,
            'nik' => $balita->nik,
            'nama' => 'Nama Balita Terupdate',
            'jenis_kelamin' => $balita->jenis_kelamin,
            'tanggal_lahir' => $balita->tanggal_lahir->format('Y-m-d'),
            'nama_ibu' => 'Ibu Baru',
            'nama_ayah' => $balita->nama_ayah,
            'no_hp_orang_tua' => '081122334455',
            'status' => 'aktif',
        ]);

        $updateResponse->assertRedirect(route('kader.warga.balita'));
        $this->assertDatabaseHas('balita', [
            'id' => $balita->id,
            'nama' => 'Nama Balita Terupdate',
            'nama_ibu' => 'Ibu Baru',
        ]);
    }

    public function test_kader_can_delete_balita()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();
        $balita = Balita::where('tapos_id', $tapos->id)->first();

        $deleteResponse = $this->actingAs($kader)->delete(route('balita.destroy', $balita));
        $deleteResponse->assertRedirect(route('kader.warga.balita'));
        $this->assertDatabaseMissing('balita', [
            'id' => $balita->id,
        ]);
    }

    public function test_kader_can_create_ibu_hamil()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();

        // 1. Visit create page
        $createResponse = $this->actingAs($kader)->get(route('ibu-hamil.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Tambah Data Ibu Hamil');

        // 2. Submit new ibu hamil
        $nik = '3203991234560002';
        $postResponse = $this->actingAs($kader)->post(route('ibu-hamil.store'), [
            'tapos_id' => $tapos->id,
            'nik' => $nik,
            'nama' => 'Ibu Siska Dewi',
            'tanggal_lahir' => '1998-08-20',
            'alamat' => 'Jl. Mawar No. 12',
            'no_hp' => '081344556677',
            'kehamilan_ke' => 2,
            'usia_kehamilan_minggu' => 14,
            'hari_pertama_haid_terakhir' => '2026-05-01',
            'status' => 'aktif',
        ]);

        $postResponse->assertRedirect(route('kader.warga.ibu-hamil'));
        $this->assertDatabaseHas('ibu_hamil', [
            'nik' => $nik,
            'nama' => 'Ibu Siska Dewi',
            'tapos_id' => $tapos->id,
        ]);
    }

    public function test_kader_can_edit_and_update_ibu_hamil()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();
        $bumil = IbuHamil::where('tapos_id', $tapos->id)->first();

        // 1. Visit edit page
        $editResponse = $this->actingAs($kader)->get(route('ibu-hamil.edit', $bumil));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Data Ibu Hamil');

        // 2. Update ibu hamil
        $updateResponse = $this->actingAs($kader)->put(route('ibu-hamil.update', $bumil), [
            'tapos_id' => $tapos->id,
            'nik' => $bumil->nik,
            'nama' => 'Ibu Hamil Terupdate',
            'tanggal_lahir' => $bumil->tanggal_lahir->format('Y-m-d'),
            'alamat' => 'Alamat Baru No. 99',
            'no_hp' => '081988776655',
            'kehamilan_ke' => $bumil->kehamilan_ke,
            'usia_kehamilan_minggu' => 20,
            'hari_pertama_haid_terakhir' => $bumil->hari_pertama_haid_terakhir?->format('Y-m-d') ?? '2026-04-10',
            'status' => 'aktif',
        ]);

        $updateResponse->assertRedirect(route('kader.warga.ibu-hamil'));
        $this->assertDatabaseHas('ibu_hamil', [
            'id' => $bumil->id,
            'nama' => 'Ibu Hamil Terupdate',
            'alamat' => 'Alamat Baru No. 99',
        ]);
    }

    public function test_kader_can_delete_ibu_hamil()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();
        $bumil = IbuHamil::where('tapos_id', $tapos->id)->first();

        $deleteResponse = $this->actingAs($kader)->delete(route('ibu-hamil.destroy', $bumil));
        $deleteResponse->assertRedirect(route('kader.warga.ibu-hamil'));
        $this->assertDatabaseMissing('ibu_hamil', [
            'id' => $bumil->id,
        ]);
    }

    public function test_kader_can_view_detail_balita_and_ibu_hamil()
    {
        $kader = User::where('role', 'kader')->first();
        $tapos = $kader->getActiveTapos();
        $balita = Balita::where('tapos_id', $tapos->id)->first();
        $bumil = IbuHamil::where('tapos_id', $tapos->id)->first();

        $bShow = $this->actingAs($kader)->get(route('balita.show', $balita));
        $bShow->assertStatus(200);
        $bShow->assertSee($balita->nama);

        $bmShow = $this->actingAs($kader)->get(route('ibu-hamil.show', $bumil));
        $bmShow->assertStatus(200);
        $bmShow->assertSee($bumil->nama);
    }
}
