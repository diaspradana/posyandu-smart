<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Puskesmas;
use App\Models\Tapos;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\JadwalPosyandu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables for fresh seed
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PemeriksaanBalita::truncate();
        PemeriksaanIbuHamil::truncate();
        JadwalPosyandu::truncate();
        Balita::truncate();
        IbuHamil::truncate();
        Tapos::truncate();
        User::truncate();
        Puskesmas::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        |--------------------------------------------------------------------------
        | Puskesmas
        |--------------------------------------------------------------------------
        */
        $puskesmas = Puskesmas::create([
            'nama' => 'Puskesmas Sehat Sejahtera',
            'alamat' => 'Jl. Pemuda No. 45, Kecamatan Sukamaju',
            'kecamatan' => 'Sukamaju',
            'kabupaten' => 'Kota Sehat',
            'latitude' => -7.2575,
            'longitude' => 112.7521,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tapos (4 Tapos Realistis sesuai data lomba)
        |--------------------------------------------------------------------------
        */
        $taposMelati = Tapos::create([
            'puskesmas_id' => $puskesmas->id,
            'nama' => 'Tapos Melati',
            'kode' => 'TAP-001',
            'alamat' => 'Jl. Melati RW 01, Kel. Sukamaju',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Sukamaju',
            'nama_ketua' => 'Siti Rahmawati',
            'no_hp' => '081234567801',
            'latitude' => -7.2570,
            'longitude' => 112.7520,
            'status' => 'aktif',
        ]);

        $taposMawar = Tapos::create([
            'puskesmas_id' => $puskesmas->id,
            'nama' => 'Tapos Mawar',
            'kode' => 'TAP-002',
            'alamat' => 'Jl. Mawar RW 02, Kel. Sukamaju',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Sukamaju',
            'nama_ketua' => 'Nur Aini',
            'no_hp' => '081234567802',
            'latitude' => -7.2590,
            'longitude' => 112.7540,
            'status' => 'aktif',
        ]);

        $taposKenanga = Tapos::create([
            'puskesmas_id' => $puskesmas->id,
            'nama' => 'Tapos Kenanga',
            'kode' => 'TAP-003',
            'alamat' => 'Jl. Kenanga RW 03, Kel. Sukamaju',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Sukamaju',
            'nama_ketua' => 'Sri Wahyuni',
            'no_hp' => '081234567803',
            'latitude' => -7.2610,
            'longitude' => 112.7560,
            'status' => 'aktif',
        ]);

        $taposDahlia = Tapos::create([
            'puskesmas_id' => $puskesmas->id,
            'nama' => 'Tapos Dahlia',
            'kode' => 'TAP-004',
            'alamat' => 'Jl. Dahlia RW 04, Kel. Sukamaju',
            'kelurahan' => 'Sukamaju',
            'kecamatan' => 'Sukamaju',
            'nama_ketua' => 'Endang Lestari',
            'no_hp' => '081234567804',
            'latitude' => -7.2630,
            'longitude' => 112.7580,
            'status' => 'aktif',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Admin & Kader Users (1 Akun Admin + 1 Akun Kader per Tapos)
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Admin Puskesmas',
            'email' => 'admin@posyandusmart.test',
            'password' => 'password123',
            'role' => 'admin',
            'puskesmas_id' => $puskesmas->id,
        ]);

        // Kader Tapos Melati (Default Kader & Email Khusus)
        User::create([
            'name' => 'Kader Tapos Melati',
            'email' => 'kader@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $puskesmas->id,
            'tapos_id' => $taposMelati->id,
        ]);

        User::create([
            'name' => 'Kader Tapos Melati',
            'email' => 'kader.melati@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $puskesmas->id,
            'tapos_id' => $taposMelati->id,
        ]);

        // Kader Tapos Mawar
        User::create([
            'name' => 'Kader Tapos Mawar',
            'email' => 'kader.mawar@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $puskesmas->id,
            'tapos_id' => $taposMawar->id,
        ]);

        // Kader Tapos Kenanga
        User::create([
            'name' => 'Kader Tapos Kenanga',
            'email' => 'kader.kenanga@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $puskesmas->id,
            'tapos_id' => $taposKenanga->id,
        ]);

        // Kader Tapos Dahlia
        User::create([
            'name' => 'Kader Tapos Dahlia',
            'email' => 'kader.dahlia@posyandusmart.test',
            'password' => 'password123',
            'role' => 'kader',
            'puskesmas_id' => $puskesmas->id,
            'tapos_id' => $taposDahlia->id,
        ]);

        $taposList = [$taposMelati, $taposMawar, $taposKenanga, $taposDahlia];





        /*
        |--------------------------------------------------------------------------
        | Balita Data Creation
        | Target numbers: Melati: 42, Mawar: 37, Kenanga: 51, Dahlia: 46 (Total ~176)
        | Stunting status distributed: Risiko (12), Pemantauan (28), Normal (126), Belum Diperiksa (10)
        |--------------------------------------------------------------------------
        */
        $firstNamesL = ['Muhammad', 'Ahmad', 'Rizky', 'Dimas', 'Alif', 'Fajar', 'Budi', 'Bayu', 'Rafi', 'Aditya', 'Gilang', 'Ilham', 'Farhan', 'Hafiz', 'Daffa', 'Kenzo', 'Arka', 'Rayan'];
        $firstNamesP = ['Aisyah', 'Citra', 'Nabila', 'Zahra', 'Putri', 'Siti', 'Anisa', 'Salma', 'Aqila', 'Khansa', 'Naura', 'Syifa', 'Alya', 'Inara', 'Mikayla', 'Clarissa', 'Talita', 'Adiba'];
        $lastNames = ['Pratama', 'Saputra', 'Ramadhan', 'Wijaya', 'Kusuma', 'Hidayat', 'Santoso', 'Nugroho', 'Firmansyah', 'Permana', 'Setiawan', 'Maulana', 'Putra', 'Putri', 'Lestari', 'Utami'];

        $balitaCounts = [
            $taposMelati->id => 42,
            $taposMawar->id => 37,
            $taposKenanga->id => 51,
            $taposDahlia->id => 46,
        ];

        $globalBalitaIndex = 1;

        // Specific named balita for display and demo
        $namedBalita = [
            ['nama' => 'Aisyah Putri', 'tapos' => $taposDahlia, 'status_stunting' => 'risiko_stunting', 'jk' => 'P', 'imunisasi' => 'tertunda'],
            ['nama' => 'Budi Santoso', 'tapos' => $taposDahlia, 'status_stunting' => 'risiko_stunting', 'jk' => 'L', 'imunisasi' => 'belum_lengkap'],
            ['nama' => 'Rafi Maulana', 'tapos' => $taposDahlia, 'status_stunting' => 'risiko_stunting', 'jk' => 'L', 'imunisasi' => 'tertunda'],
            ['nama' => 'Citra Lestari', 'tapos' => $taposKenanga, 'status_stunting' => 'pemantauan', 'jk' => 'P', 'imunisasi' => 'lengkap'],
            ['nama' => 'Dimas Pratama', 'tapos' => $taposKenanga, 'status_stunting' => 'normal', 'jk' => 'L', 'imunisasi' => 'lengkap'],
            ['nama' => 'Nabila Zahra', 'tapos' => $taposMelati, 'status_stunting' => 'normal', 'jk' => 'P', 'imunisasi' => 'lengkap'],
            ['nama' => 'Alif Ramadhan', 'tapos' => $taposMawar, 'status_stunting' => 'normal', 'jk' => 'L', 'imunisasi' => 'lengkap'],
        ];

        $balitaList = [];

        foreach ($taposList as $tapos) {
            $count = $balitaCounts[$tapos->id];
            for ($i = 1; $i <= $count; $i++) {
                $jk = ($i % 2 === 0) ? 'P' : 'L';
                $fn = ($jk === 'L') ? $firstNamesL[array_rand($firstNamesL)] : $firstNamesP[array_rand($firstNamesP)];
                $ln = $lastNames[array_rand($lastNames)];
                $nama = "$fn $ln";

                // Pick specific named ones if available
                if ($tapos->id === $taposDahlia->id && $i === 1) $nama = 'Aisyah Putri';
                if ($tapos->id === $taposDahlia->id && $i === 2) $nama = 'Budi Santoso';
                if ($tapos->id === $taposDahlia->id && $i === 3) $nama = 'Rafi Maulana';
                if ($tapos->id === $taposMelati->id && $i === 1) $nama = 'Citra Lestari';
                if ($tapos->id === $taposKenanga->id && $i === 1) $nama = 'Dimas Pratama';

                $birthDate = now()->subMonths(rand(3, 58))->subDays(rand(1, 28))->format('Y-m-d');
                $nik = sprintf('3201%02d%06d%04d', $tapos->id, rand(100000, 999999), $globalBalitaIndex);

                $balita = Balita::create([
                    'tapos_id' => $tapos->id,
                    'nik' => $nik,
                    'nama' => $nama,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $birthDate,
                    'nama_ibu' => 'Ibu ' . $firstNamesP[array_rand($firstNamesP)],
                    'nama_ayah' => 'Bpk. ' . $firstNamesL[array_rand($firstNamesL)],
                    'no_hp_orang_tua' => sprintf('08%010d', rand(1000000000, 9999999999)),
                    'status' => 'aktif',
                ]);

                $balitaList[] = $balita;
                $globalBalitaIndex++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ibu Hamil Data Creation
        | Melati: 8, Mawar: 6, Kenanga: 9, Dahlia: 7 (Total ~30)
        |--------------------------------------------------------------------------
        */
        $bumilCounts = [
            $taposMelati->id => 8,
            $taposMawar->id => 6,
            $taposKenanga->id => 9,
            $taposDahlia->id => 7,
        ];

        $bumilList = [];
        $globalBumilIndex = 1;

        foreach ($taposList as $tapos) {
            $count = $bumilCounts[$tapos->id];
            for ($i = 1; $i <= $count; $i++) {
                $nama = $firstNamesP[array_rand($firstNamesP)] . ' ' . $lastNames[array_rand($lastNames)];
                $birthDate = now()->subYears(rand(21, 36))->subDays(rand(1, 300))->format('Y-m-d');
                $usiaMinggu = rand(6, 36);
                $hpht = now()->subWeeks($usiaMinggu)->format('Y-m-d');
                $nik = sprintf('3203%02d%06d%04d', $tapos->id, rand(100000, 999999), $globalBumilIndex);

                $bumil = IbuHamil::create([
                    'tapos_id' => $tapos->id,
                    'nik' => $nik,
                    'nama' => $nama,
                    'tanggal_lahir' => $birthDate,
                    'alamat' => $tapos->alamat,
                    'no_hp' => sprintf('08%010d', rand(1000000000, 9999999999)),
                    'hari_pertama_haid_terakhir' => $hpht,
                    'kehamilan_ke' => rand(1, 3),
                    'usia_kehamilan_minggu' => $usiaMinggu,
                    'status' => 'aktif',
                ]);

                $bumilList[] = $bumil;
                $globalBumilIndex++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Balita Bulanan (Jan - Agu 2026)
        | Tapos Melati Kehadiran: ~92%
        | Tapos Mawar Kehadiran: ~87%
        | Tapos Kenanga Kehadiran: ~71%
        | Tapos Dahlia Kehadiran: ~62%
        |--------------------------------------------------------------------------
        */
        $attendanceRates = [
            $taposMelati->id => 92,
            $taposMawar->id => 87,
            $taposKenanga->id => 71,
            $taposDahlia->id => 62,
        ];

        $months = [
            1 => '2026-01-15',
            2 => '2026-02-15',
            3 => '2026-03-15',
            4 => '2026-04-15',
            5 => '2026-05-15',
            6 => '2026-06-15',
            7 => '2026-07-15',
            8 => '2026-08-15',
        ];

        foreach ($balitaList as $idx => $balita) {
            $taposId = $balita->tapos_id;
            $rate = $attendanceRates[$taposId];

            // Assign stunting profile
            $stuntingProfile = 'normal';
            $imunisasiProfile = 'lengkap';

            // Tapos Dahlia has high stunting risk (3 high risk + 6 pemantauan)
            if ($taposId === $taposDahlia->id) {
                if ($idx % 46 < 3) {
                    $stuntingProfile = 'risiko_stunting';
                    $imunisasiProfile = 'tertunda';
                } elseif ($idx % 46 < 9) {
                    $stuntingProfile = 'pemantauan';
                    $imunisasiProfile = 'belum_lengkap';
                }
            } elseif ($taposId === $taposKenanga->id) {
                if ($idx % 51 < 4) {
                    $stuntingProfile = 'risiko_stunting';
                    $imunisasiProfile = 'belum_lengkap';
                } elseif ($idx % 51 < 14) {
                    $stuntingProfile = 'pemantauan';
                }
            } elseif ($taposId === $taposMawar->id) {
                if ($idx % 37 < 3) {
                    $stuntingProfile = 'risiko_stunting';
                } elseif ($idx % 37 < 8) {
                    $stuntingProfile = 'pemantauan';
                }
            } else { // Tapos Melati
                if ($idx % 42 < 2) {
                    $stuntingProfile = 'risiko_stunting';
                } elseif ($idx % 42 < 7) {
                    $stuntingProfile = 'pemantauan';
                }
            }

            // Some children never examined (10 children)
            if ($idx < 10 && $taposId === $taposKenanga->id) {
                continue; // Belum diperiksa sama sekali
            }

            foreach ($months as $mNum => $dateStr) {
                // Determine attendance for this month
                $hadir = (rand(1, 100) <= $rate);

                if ($hadir) {
                    $bb = rand(80, 160) / 10;
                    $tb = rand(700, 1050) / 10;
                    $lk = rand(420, 490) / 10;
                    $prob = ($stuntingProfile === 'risiko_stunting') ? (rand(78, 92) / 100) : (($stuntingProfile === 'pemantauan') ? (rand(65, 76) / 100) : (rand(85, 96) / 100));

                    PemeriksaanBalita::create([
                        'balita_id' => $balita->id,
                        'tanggal_pemeriksaan' => $dateStr,
                        'umur_bulan' => max(1, $balita->usia_bulan - (8 - $mNum)),
                        'berat_badan' => $bb,
                        'tinggi_badan' => $tb,
                        'lingkar_kepala' => $lk,
                        'status_stunting' => $stuntingProfile,
                        'hasil_ai' => $stuntingProfile,
                        'ai_probability' => $prob,
                        'ai_probabilities' => [
                            'risiko_stunting' => ($stuntingProfile === 'risiko_stunting' ? $prob : 0.05),
                            'pemantauan' => ($stuntingProfile === 'pemantauan' ? $prob : 0.10),
                            'normal' => ($stuntingProfile === 'normal' ? $prob : 0.08),
                        ],
                        'ai_model_version' => 'stunting-v1.0',
                        'status_imunisasi' => $imunisasiProfile,
                        'status_pemeriksaan' => 'diperiksa',
                        'status_validasi' => ($mNum == 8 && $idx % 3 === 0) ? 'pending' : 'validated',
                        'catatan_validasi' => ($mNum == 8 && $idx % 3 === 0) ? null : 'Tervalidasi oleh Bidan Puskesmas.',
                        'catatan' => ($stuntingProfile === 'risiko_stunting') ? 'Perlu pendampingan PMT pemulihan.' : null,
                        'kehadiran' => true,
                    ]);
                } else {
                    PemeriksaanBalita::create([
                        'balita_id' => $balita->id,
                        'tanggal_pemeriksaan' => $dateStr,
                        'umur_bulan' => max(1, $balita->usia_bulan - (8 - $mNum)),
                        'berat_badan' => 0,
                        'tinggi_badan' => 0,
                        'status_stunting' => 'normal',
                        'hasil_ai' => 'belum_diperiksa',
                        'ai_probability' => null,
                        'ai_model_version' => 'stunting-v1.0',
                        'status_imunisasi' => 'lengkap',
                        'status_pemeriksaan' => 'belum_diperiksa',
                        'status_validasi' => 'validated',
                        'kehadiran' => false,
                    ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Ibu Hamil Bulanan
        |--------------------------------------------------------------------------
        */
        foreach ($bumilList as $idx => $bumil) {
            $taposId = $bumil->tapos_id;
            $rate = $attendanceRates[$taposId];

            // 15% bumil belum periksa
            $isUnchecked = ($idx % 6 === 0);

            // Risk profile for maternal
            $maternalRisk = 'low';
            if ($idx % 7 === 0) {
                $maternalRisk = 'high';
            } elseif ($idx % 4 === 0) {
                $maternalRisk = 'medium';
            }

            foreach ($months as $mNum => $dateStr) {
                $hadir = !$isUnchecked && (rand(1, 100) <= $rate);

                if ($hadir) {
                    $bb = rand(54, 76) + (rand(0, 9) / 10);
                    $sistole = ($maternalRisk === 'high') ? rand(140, 155) : (($maternalRisk === 'medium') ? rand(126, 136) : rand(110, 122));
                    $diastole = ($maternalRisk === 'high') ? rand(90, 100) : (($maternalRisk === 'medium') ? rand(82, 88) : rand(70, 80));
                    $bs = ($maternalRisk === 'high') ? (rand(95, 130) / 10) : (($maternalRisk === 'medium') ? (rand(78, 88) / 10) : (rand(68, 76) / 10));
                    $temp = 98.6;
                    $hr = ($maternalRisk === 'high') ? rand(85, 96) : rand(72, 80);
                    $prob = ($maternalRisk === 'high') ? (rand(82, 94) / 100) : (($maternalRisk === 'medium') ? (rand(68, 80) / 100) : (rand(86, 96) / 100));

                    PemeriksaanIbuHamil::create([
                        'ibu_hamil_id' => $bumil->id,
                        'tanggal_pemeriksaan' => $dateStr,
                        'berat_badan' => $bb,
                        'tekanan_darah' => "$sistole/$diastole",
                        'systolic_bp' => $sistole,
                        'diastolic_bp' => $diastole,
                        'blood_sugar' => $bs,
                        'body_temp' => $temp,
                        'heart_rate' => $hr,
                        'usia_kehamilan_minggu' => min(40, ($bumil->usia_kehamilan_minggu ?? 12) + $mNum),
                        'ai_risk_level' => $maternalRisk,
                        'ai_probability' => $prob,
                        'ai_probabilities' => [
                            'high' => ($maternalRisk === 'high' ? $prob : 0.05),
                            'medium' => ($maternalRisk === 'medium' ? $prob : 0.12),
                            'low' => ($maternalRisk === 'low' ? $prob : 0.08),
                        ],
                        'ai_model_version' => 'maternal-v1.0',
                        'status_pemeriksaan' => 'diperiksa',
                        'status_validasi' => ($mNum == 8 && $idx % 2 === 0) ? 'pending' : 'validated',
                        'catatan_validasi' => ($mNum == 8 && $idx % 2 === 0) ? null : 'Tervalidasi Bidan Puskesmas.',
                        'catatan' => ($maternalRisk === 'high') ? 'Tekanan darah perlu dipantau berkala.' : null,
                        'kehadiran' => true,
                    ]);
                } else {
                    PemeriksaanIbuHamil::create([
                        'ibu_hamil_id' => $bumil->id,
                        'tanggal_pemeriksaan' => $dateStr,
                        'berat_badan' => 0,
                        'tekanan_darah' => '0/0',
                        'usia_kehamilan_minggu' => 0,
                        'ai_risk_level' => null,
                        'ai_probability' => null,
                        'ai_model_version' => 'maternal-v1.0',
                        'status_pemeriksaan' => 'belum_diperiksa',
                        'status_validasi' => 'validated',
                        'kehadiran' => false,
                    ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Jadwal Posyandu (Dibuat Admin Puskesmas untuk Masing-Masing Tapos)
        |--------------------------------------------------------------------------
        */
        // 1. Tapos Melati
        JadwalPosyandu::create([
            'tapos_id' => $taposMelati->id,
            'nama_kegiatan' => 'Posyandu Balita & Imunisasi Rutin',
            'jenis_kegiatan' => 'Posyandu Balita',
            'tanggal' => '2026-09-05',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Balai RW 05 Melati',
            'catatan' => 'Mohon kader menyiapkan timbangan dacin, pengukur tinggi badan, dan KMS lengkap. Prioritas penimbangan balita stunting.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'usulan_perubahan',
            'alasan_perubahan' => 'Kegiatan Tapos Melati pada 5 September tidak dapat dilaksanakan karena lokasi balai RW digunakan untuk kegiatan rapat warga. Diusulkan geser ke 7 September 2026.',
            'usulan_tanggal' => '2026-09-07',
            'usulan_waktu_mulai' => '08:30',
            'usulan_waktu_selesai' => '11:30',
            'usulan_lokasi' => 'Gedung PAUD Melati RW 01',
        ]);

        JadwalPosyandu::create([
            'tapos_id' => $taposMelati->id,
            'nama_kegiatan' => 'Posyandu Balita & Skrining Tumbuh Kembang',
            'jenis_kegiatan' => 'Posyandu Balita',
            'tanggal' => '2026-09-12',
            'waktu_mulai' => '08:30',
            'waktu_selesai' => '11:30',
            'lokasi' => 'Balai RW 01 Melati',
            'catatan' => 'Pemeriksaan deteksi dini stunting, penimbangan BB/TB, dan vitamin A.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'siap',
        ]);

        JadwalPosyandu::create([
            'tapos_id' => $taposMelati->id,
            'nama_kegiatan' => 'Posyandu Ibu Hamil & Kelas Nutrisi KIA',
            'jenis_kegiatan' => 'Posyandu Ibu Hamil',
            'tanggal' => '2026-09-19',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:30',
            'lokasi' => 'Balai RW 01 Melati',
            'catatan' => 'Pengukuran LILA, pemantauan kenaikan BB trimester 2 & 3, dan pemberian tablet tambah darah.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'menunggu_konfirmasi',
        ]);

        // 2. Tapos Mawar
        JadwalPosyandu::create([
            'tapos_id' => $taposMawar->id,
            'nama_kegiatan' => 'Posyandu Balita & Pemberian PMT',
            'jenis_kegiatan' => 'Posyandu Balita',
            'tanggal' => '2026-09-07',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Balai Posyandu Mawar RW 02',
            'catatan' => 'Pemberian PMT pemulihan berbasis telur dan kelor untuk balita gizi kurang.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'siap',
        ]);

        JadwalPosyandu::create([
            'tapos_id' => $taposMawar->id,
            'nama_kegiatan' => 'Posyandu Ibu Hamil & Senam Hamil',
            'jenis_kegiatan' => 'Posyandu Ibu Hamil',
            'tanggal' => '2026-09-15',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:30',
            'lokasi' => 'Balai RW 02 Mawar',
            'catatan' => 'Pemeriksaan tensi darah bumil dan latihan senam hamil sehat bersama bidan.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'menunggu_konfirmasi',
        ]);

        // 3. Tapos Kenanga
        JadwalPosyandu::create([
            'tapos_id' => $taposKenanga->id,
            'nama_kegiatan' => 'Posyandu Balita Terpadu',
            'jenis_kegiatan' => 'Posyandu Balita',
            'tanggal' => '2026-09-10',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Balai RW 03 Kenanga',
            'catatan' => 'Penimbangan serentak dan edukasi MP-ASI kaya protein hewani.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'siap',
        ]);

        JadwalPosyandu::create([
            'tapos_id' => $taposKenanga->id,
            'nama_kegiatan' => 'Posyandu Integrasi (Balita & Ibu Hamil)',
            'jenis_kegiatan' => 'Posyandu Integrasi (Balita & Ibu Hamil)',
            'tanggal' => '2026-09-22',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Balai Pertemuan Kenanga RW 03',
            'catatan' => 'Pelayanan terintegrasi penimbangan balita dan pemeriksaan berkala ibu hamil.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'menunggu_konfirmasi',
        ]);

        // 4. Tapos Dahlia
        JadwalPosyandu::create([
            'tapos_id' => $taposDahlia->id,
            'nama_kegiatan' => 'Posyandu Balita & Skrining Tumbuh Kembang',
            'jenis_kegiatan' => 'Posyandu Balita',
            'tanggal' => '2026-09-11',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '11:30',
            'lokasi' => 'Balai RW 04 Dahlia',
            'catatan' => 'Skrining KPSP dan konsultasi tumbuh kembang anak dengan bidan desa.',
            'status' => 'mendatang',
            'status_konfirmasi' => 'menunggu_konfirmasi',
        ]);
    }
}