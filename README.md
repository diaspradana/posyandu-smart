# Posyandu Smart

**Sistem Informasi Monitoring Kesehatan Balita dan Ibu Hamil Berbasis Web dengan AI-Assisted Screening**

Posyandu Smart adalah aplikasi berbasis web untuk membantu Puskesmas dan Kader Posyandu mengelola data Balita, Ibu Hamil, Tapos, pemeriksaan, jadwal Posyandu, kehadiran, validasi pemeriksaan, monitoring, laporan, dan hasil screening berbantuan AI.

> **Catatan penting:** fitur AI pada aplikasi ini merupakan **screening awal / decision support**, bukan diagnosis medis. Hasil AI tidak menggantikan pemeriksaan dan keputusan tenaga kesehatan.

## 1. Fitur Utama

### Admin Puskesmas
- Dashboard monitoring wilayah Puskesmas.
- Monitoring Balita dan Ibu Hamil.
- CRUD Tapos, Balita, dan Ibu Hamil.
- Monitoring dan validasi hasil pemeriksaan yang diinput Kader.
- Pengelolaan jadwal Posyandu.
- Rekap kehadiran dan laporan.
- Pemantauan hasil screening AI pada tingkat individu dan wilayah.

### Kader Posyandu
- Dashboard khusus Tapos yang ditugaskan.
- Pengelolaan data Balita dan Ibu Hamil pada Tapos.
- Input pemeriksaan Balita dan Ibu Hamil.
- Screening AI saat pemeriksaan.
- Monitoring riwayat dan tren hasil screening.
- Melihat jadwal dan melakukan konfirmasi kesiapan / usulan perubahan.
- Pengelolaan kehadiran dan laporan.
- Profil Kader.

## 2. AI-Assisted Screening

Aplikasi menggunakan microservice Python/FastAPI yang terpisah dari Laravel.

### Screening Ibu Hamil
- Input model: Age, SystolicBP, DiastolicBP, BS, BodyTemp, HeartRate.
- Output: `low`, `medium`, atau `high` risk beserta probabilitas, indikator, rekomendasi tindak lanjut, dan versi model.
- Dataset/model disimpan pada `ai_service/data` dan `ai_service/models` pada paket pengembangan ini.

### Screening Balita
- Input model mencakup umur, jenis kelamin, berat badan, tinggi badan, serta fitur profil yang digunakan oleh model.
- Output screening: normal / pemantauan / risiko stunting sesuai implementasi model.
- Riwayat pemeriksaan disimpan per kunjungan sehingga hasil dapat dipantau secara longitudinal.

## 3. Arsitektur Singkat

```text
Browser
   |
   v
Laravel 12 (Web Application)
   |
   +---- MySQL
   |
   +---- FastAPI AI Service
             |
             +---- Maternal Risk Model
             +---- Balita Growth/Stunting Model
```

## 4. Role dan Akses

| Role | Cakupan |
|---|---|
| Admin Puskesmas | Seluruh Tapos di bawah Puskesmas yang terkait |
| Kader | Tapos yang ditugaskan pada akun Kader |

Satu Tapos dapat memiliki lebih dari satu akun Kader. Relasi Kader ke Tapos disimpan pada tabel `users` melalui `tapos_id`.

## 5. Teknologi

- **Backend:** Laravel 12 / PHP 8.2+
- **Database:** MySQL
- **Frontend:** Blade, Tailwind CSS, Vite, JavaScript
- **AI Service:** Python, FastAPI, scikit-learn, pandas, NumPy, joblib
- **Authentication:** Laravel session authentication + role middleware
- **API communication:** HTTP request dari Laravel ke FastAPI

Detail teknologi dan versi ada di `docs/TECHNOLOGY.md`.

## 6. Struktur Repository

```text
posyandu-smart/
├── ai_service/                 # FastAPI + model AI
│   ├── data/                   # Dataset AI
│   ├── models/                 # Model dan metadata
│   ├── main.py                 # API inference
│   ├── train_balita.py         # Training model Balita
│   ├── train_maternal.py       # Training model Ibu Hamil
│   └── requirements.txt
├── app/
│   ├── Http/Controllers/       # Controller Laravel
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policy
│   ├── Providers/
│   └── Services/               # Service layer, termasuk AI
├── database/
│   ├── migrations/             # Struktur database
│   ├── seeders/                # Data demo
│   └── factories/
├── resources/views/             # Blade views
├── routes/web.php               # Routing aplikasi
├── public/                      # Public assets
├── tests/                       # Feature dan unit test
├── docs/
│   ├── INSTALLATION.md
│   ├── TECHNOLOGY.md
│   └── TECHNICAL-DOCUMENTATION.md
├── composer.json
├── package.json
└── README.md
```

## 7. Instalasi Cepat

Persyaratan minimum:
- PHP 8.2+
- Composer
- Node.js + npm
- MySQL 8.x / MariaDB yang kompatibel
- Python 3.10+

Langkah utama:

```bash
git clone <URL-REPOSITORY>
cd posyandu-smart
composer install
copy .env.example .env
php artisan key:generate
```

Buat database `posyandu_smart`, lalu sesuaikan `.env` dan jalankan:

```bash
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Untuk menjalankan AI service:

```bash
cd ai_service
python -m venv .venv
# Windows
.venv\Scripts\activate
# Linux/macOS
# source .venv/bin/activate
pip install -r requirements.txt
uvicorn main:app --host 0.0.0.0 --port 8001
```

Panduan lengkap ada di `docs/INSTALLATION.md`.

## 8. Akun Demo

Seeder menyediakan akun demo. **Password di bawah hanya untuk lingkungan pengembangan/demo dan harus diganti pada deployment nyata.**

| Role | Email | Password |
|---|---|---|
| Admin | `admin@posyandusmart.test` | `password123` |
| Kader Melati | `kader@posyandusmart.test` | `password123` |
| Kader Melati | `kader.melati@posyandusmart.test` | `password123` |
| Kader Mawar | `kader.mawar@posyandusmart.test` | `password123` |
| Kader Kenanga | `kader.kenanga@posyandusmart.test` | `password123` |
| Kader Dahlia | `kader.dahlia@posyandusmart.test` | `password123` |

## 9. Dokumentasi

- [Installation Guide](docs/INSTALLATION.md)
- [Technology Information](docs/TECHNOLOGY.md)
- [Technical Documentation](docs/TECHNICAL-DOCUMENTATION.md)

## 10. Keamanan dan Privasi

- Jangan commit `.env` atau credential produksi.
- Jangan memasukkan NIK, nomor telepon, alamat lengkap, atau rekam kesehatan nyata ke repository publik.
- Untuk demo publik, gunakan data sintetis/dummy.
- Ganti seluruh password akun demo sebelum deployment produksi.
- Batasi CORS FastAPI pada domain aplikasi ketika deployment produksi.
- Gunakan HTTPS pada deployment publik.
- AI hanya digunakan sebagai screening / decision support.

## 11. Lisensi

Kode aplikasi menggunakan lisensi MIT mengikuti konfigurasi project Laravel. Dataset pihak ketiga dan model AI tetap mengikuti lisensi/syarat sumber dataset masing-masing.
