# Technical Documentation — Posyandu Smart

## 1. System Overview

Posyandu Smart menggabungkan aplikasi web Laravel, database relasional MySQL, dan microservice AI berbasis FastAPI.

```text
+-----------------------+
|       Browser         |
+-----------+-----------+
            |
            v
+-----------------------+
| Laravel 12 Application|
| Auth / Role / CRUD     |
| Monitoring / Reports   |
+----+--------------+---+
     |              |
     v              v
+---------+   +------------------+
|  MySQL  |   | FastAPI AI       |
| Database|   | Microservice     |
+---------+   +--------+---------+
                       |
                +------+------+
                | ML Models   |
                | Maternal    |
                | Balita      |
                +-------------+
```

## 2. Main Modules

### Authentication
`AuthController` menangani login/logout. User diarahkan ke dashboard berdasarkan role.

### Dashboard
`DashboardController` menyediakan dashboard dan monitoring Admin Puskesmas.

`KaderController` menyediakan dashboard dan modul operasional Kader.

### Master Data
- `TaposController`
- `BalitaController`
- `IbuHamilController`

Ketiga resource memiliki route CRUD dan pembatasan akses melalui middleware/policy.

### Pemeriksaan
`PemeriksaanController` menyediakan tampilan riwayat pemeriksaan.

Input pemeriksaan Kader dikelola melalui `KaderController` dan disimpan pada:

- `pemeriksaan_balita`
- `pemeriksaan_ibu_hamil`

### Validasi
`ValidasiPemeriksaanController` menyediakan proses approve/reject oleh Admin Puskesmas.

Status validasi yang digunakan:

```text
pending -> validated
pending -> rejected
```

### Jadwal
`JadwalAdminController` menangani pembuatan dan pengelolaan jadwal oleh Admin. Kader dapat mengonfirmasi kesiapan atau mengajukan perubahan melalui `KaderController`.

## 3. Database Entities

Migration utama membentuk entitas berikut:

```text
users
puskesmas
Tapos
Balita
Ibu Hamil
Pemeriksaan Balita
Pemeriksaan Ibu Hamil
Jadwal Posyandu
```

Relasi inti:

```text
puskesmas 1 --- N tapos
puskesmas 1 --- N users

tapos 1 --- N balita
tapos 1 --- N ibu_hamil
tapos 1 --- N users
tapos 1 --- N jadwal_posyandu

balita 1 --- N pemeriksaan_balita
ibu_hamil 1 --- N pemeriksaan_ibu_hamil
```

## 4. Role-Based Access

### Admin Puskesmas
Admin memiliki `puskesmas_id` dan tidak dibatasi ke satu Tapos. Data yang ditampilkan mencakup Tapos dalam Puskesmas terkait.

### Kader
Kader memiliki `puskesmas_id` dan `tapos_id`. Operasi data warga dibatasi pada Tapos yang ditugaskan.

## 5. Examination Flow

```text
Kader memilih warga
        |
        v
Input pemeriksaan
        |
        v
Laravel validasi input
        |
        v
FastAPI AI screening
        |
        v
Simpan hasil + probabilitas + versi model
        |
        v
Status validasi = pending
        |
        v
Admin Puskesmas review
        |
   +----+----+
   |         |
Approve    Reject
   |         |
   v         v
Official    Rejected
record      record
```

## 6. Longitudinal Monitoring

Setiap pemeriksaan disimpan sebagai record terpisah. Karena itu satu Balita/Ibu Hamil dapat memiliki banyak histori pemeriksaan.

Contoh konsep Balita:

```text
Kunjungan 1 -> hasil AI
Kunjungan 2 -> hasil AI
Kunjungan 3 -> hasil AI
       |
       v
Riwayat / trend monitoring
```

Laravel dapat menampilkan perubahan status dari histori tersebut. Hasil tiap kunjungan tetap merupakan screening pada saat pemeriksaan, bukan diagnosis permanen.

## 7. AI API

### Health Check

```http
GET /health
```

### Maternal Prediction

```http
POST /api/predict/maternal
Content-Type: application/json
```

Contoh request:

```json
{
  "age": 28,
  "systolic_bp": 120,
  "diastolic_bp": 80,
  "blood_sugar": 7.1,
  "body_temp": 98.6,
  "heart_rate": 76
}
```

Response utama:

```json
{
  "risk_level": "low",
  "probability": 0.0,
  "probabilities": {},
  "status_label": "...",
  "indicators": [],
  "recommendation": "...",
  "disclaimer": "...",
  "model_version": "maternal-v1.0"
}
```

Nilai probabilitas aktual diisi oleh model ketika model berhasil dimuat.

### Balita Prediction

```http
POST /api/predict/balita
Content-Type: application/json
```

Input utama:

- `umur_bulan`
- `jenis_kelamin`
- `berat_badan`
- `tinggi_badan`
- `lingkar_kepala` (opsional)
- `berat_lahir` (opsional)
- `asi_eksklusif` (opsional)

## 8. Model Versioning

Metadata model menyimpan:

- nama algoritma
- versi model
- feature columns
- target classes
- benchmark metrics
- waktu pembuatan

Contoh versi:

```text
maternal-v1.0
stunting-v1.0
```

## 9. Security Considerations

1. `.env` tidak boleh masuk Git.
2. Password user harus menggunakan hashing Laravel.
3. Role middleware digunakan pada route sensitif.
4. Policy digunakan untuk otorisasi resource tertentu.
5. Production harus menggunakan HTTPS.
6. CORS FastAPI sebaiknya tidak menggunakan wildcard.
7. Data kesehatan tidak boleh dipublikasikan tanpa dasar hukum/izin yang sesuai.
8. Demo publik sebaiknya menggunakan data sintetis.

## 10. Deployment Considerations

Komponen dapat dideploy terpisah:

```text
GitHub
  |
  +-- Laravel Web App
  |
  +-- FastAPI AI Service
  |
  +-- External MySQL
```

Environment variable harus disediakan pada platform deployment, bukan disimpan pada repository.

## 11. Known Technical Notes

- Repository ini memisahkan Laravel dan AI service agar model Python tidak bergantung pada runtime PHP.
- Model `.pkl` menggunakan joblib/scikit-learn dan sebaiknya dijalankan dengan versi dependency yang kompatibel.
- Untuk deployment publik, data dummy/sintetis direkomendasikan untuk demonstrasi.
- AI adalah decision support/screening dan bukan alat diagnosis medis.
