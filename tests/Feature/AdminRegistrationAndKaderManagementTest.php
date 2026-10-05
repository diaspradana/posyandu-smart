<?php

namespace Tests\Feature;

use App\Models\Puskesmas;
use App\Models\Tapos;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRegistrationAndKaderManagementTest extends TestCase
{
    use RefreshDatabase;

    private Puskesmas $puskesmas;
    private Tapos $tapos;
    private User $admin;
    private User $kader;

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
    }

    public function test_guest_can_view_register_admin_page(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Admin Puskesmas');
        $response->assertSee('Khusus Admin Puskesmas');
        $response->assertSee('Master Data Tapos');
    }

    public function test_guest_can_register_as_admin_with_existing_puskesmas(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'dr. Budi Santoso',
            'email' => 'budi.admin@posyandusmart.test',
            'puskesmas_id' => $this->puskesmas->id,
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi.admin@posyandusmart.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals($this->puskesmas->id, $user->puskesmas_id);
    }

    public function test_guest_can_register_as_admin_with_new_puskesmas(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'dr. Citra Lestari',
            'email' => 'citra.admin@posyandusmart.test',
            'puskesmas_nama' => 'Puskesmas Melati Sejahtera',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'citra.admin@posyandusmart.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role);
        $this->assertNotNull($user->puskesmas);
        $this->assertEquals('Puskesmas Melati Sejahtera', $user->puskesmas->nama);
    }

    public function test_admin_registration_validation_fails_for_duplicate_email(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'Admin Duplikat',
            'email' => 'admin@posyandusmart.test', // already exists
            'puskesmas_id' => $this->puskesmas->id,
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_admin_registration_validation_fails_for_mismatched_password(): void
    {
        $response = $this->post(route('register.process'), [
            'name' => 'Admin Pass Beda',
            'email' => 'passbeda@posyandusmart.test',
            'puskesmas_id' => $this->puskesmas->id,
            'password' => 'secret12345',
            'password_confirmation' => 'different12345',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_admin_can_view_kader_list_in_tapos_show(): void
    {
        $response = $this->actingAs($this->admin)->get(route('tapos.show', $this->tapos));

        $response->assertStatus(200);
        $response->assertSee('Daftar Petugas', false);
        $response->assertSee('Kader Melati 1');
        $response->assertSee('kader1@posyandusmart.test');
        $response->assertSee('Registrasi Kader Baru');
    }

    public function test_admin_can_register_kader_through_master_data_tapos(): void
    {
        $response = $this->actingAs($this->admin)->post(route('tapos.kader.store', $this->tapos), [
            'name' => 'Siti Nurhaliza',
            'email' => 'siti.kader@posyandusmart.test',
            'password' => 'kaderpass123',
            'password_confirmation' => 'kaderpass123',
        ]);

        $response->assertRedirect(route('tapos.show', $this->tapos));
        $response->assertSessionHas('success');

        $kaderUser = User::where('email', 'siti.kader@posyandusmart.test')->first();
        $this->assertNotNull($kaderUser);
        $this->assertEquals('kader', $kaderUser->role);
        $this->assertEquals($this->tapos->id, $kaderUser->tapos_id);
        $this->assertEquals($this->puskesmas->id, $kaderUser->puskesmas_id);
    }

    public function test_kader_registered_by_admin_can_login_and_access_kader_dashboard(): void
    {
        // Admin registers kader
        $this->actingAs($this->admin)->post(route('tapos.kader.store', $this->tapos), [
            'name' => 'Dewi Lestari',
            'email' => 'dewi.kader@posyandusmart.test',
            'password' => 'kaderpass123',
            'password_confirmation' => 'kaderpass123',
        ]);

        // Logout admin
        $this->post(route('logout'));
        $this->assertGuest();

        // Kader attempts login
        $loginResponse = $this->post(route('login.process'), [
            'email' => 'dewi.kader@posyandusmart.test',
            'password' => 'kaderpass123',
        ]);

        $loginResponse->assertRedirect(route('kader.dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('kader', auth()->user()->role);
    }

    public function test_admin_can_update_kader_account(): void
    {
        $response = $this->actingAs($this->admin)->put(route('tapos.kader.update', [
            'tapo' => $this->tapos->id,
            'user' => $this->kader->id,
        ]), [
            'name' => 'Kader Melati Updated',
            'email' => 'kader1.updated@posyandusmart.test',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('tapos.show', $this->tapos));
        $response->assertSessionHas('success');

        $this->kader->refresh();
        $this->assertEquals('Kader Melati Updated', $this->kader->name);
        $this->assertEquals('kader1.updated@posyandusmart.test', $this->kader->email);
    }

    public function test_admin_can_delete_kader_account(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('tapos.kader.destroy', [
            'tapo' => $this->tapos->id,
            'user' => $this->kader->id,
        ]));

        $response->assertRedirect(route('tapos.show', $this->tapos));
        $response->assertSessionHas('success');

        $this->assertNull(User::find($this->kader->id));
    }
}
