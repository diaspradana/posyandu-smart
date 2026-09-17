# Technology Information

## 1. Application Stack

| Layer | Technology | Peran |
|---|---|---|
| Web framework | Laravel 12 | Backend MVC, routing, validation, ORM |
| Language | PHP 8.2+ | Backend application |
| ORM | Eloquent | Akses dan relasi database |
| Database | MySQL | Penyimpanan data operasional |
| Template | Blade | Server-rendered UI |
| CSS | Tailwind CSS | Styling responsive |
| Build tool | Vite | Build asset frontend |
| JavaScript | JavaScript + Axios | Interaksi frontend |
| AI API | FastAPI | Microservice inference |
| AI/ML | scikit-learn | Training dan inference model |
| Data processing | pandas + NumPy | Pengolahan dataset |
| Model serialization | joblib | Penyimpanan model |
| API schema | Pydantic | Validasi request/response AI |

## 2. Dependency Sources

Laravel dependencies didefinisikan pada `composer.json` dan dikunci pada `composer.lock`.

Frontend dependencies didefinisikan pada `package.json` dan dikunci pada `package-lock.json`.

AI dependencies didefinisikan pada `ai_service/requirements.txt`.

## 3. Authentication and Authorization

Authentication menggunakan mekanisme session Laravel. Akses role dibatasi menggunakan middleware `RoleMiddleware`.

Role utama:

- `admin`
- `kader`

Relasi wilayah:

```text
Puskesmas
   └── Tapos
        ├── Balita
        ├── Ibu Hamil
        └── Kader (users)
```

## 4. AI Technology

Model AI tidak dijalankan langsung di PHP. Laravel berkomunikasi dengan FastAPI sebagai service terpisah.

```text
Laravel
   |
   | HTTP
   v
FastAPI
   |
   +-- maternal_model.pkl
   +-- stunting_model.pkl
```

### Maternal
Dataset yang digunakan oleh pipeline adalah Maternal Health Risk Dataset. Fitur model yang didefinisikan pada pipeline:

- Age
- SystolicBP
- DiastolicBP
- BS
- BodyTemp
- HeartRate

### Balita
Pipeline training berada pada `train_balita.py`. Feature columns dan metadata final mengikuti file metadata model yang tersedia di repository.

## 5. Development Environment

Contoh konfigurasi lokal:

```text
Laravel : http://127.0.0.1:8000
FastAPI : http://127.0.0.1:8001
MySQL   : 127.0.0.1:3306
```

## 6. Production Notes

- Gunakan environment variable untuk credential.
- Jangan commit `.env`.
- Gunakan HTTPS.
- Batasi CORS FastAPI dari `*` menjadi domain aplikasi yang diperlukan.
- Sediakan backup database.
- Ganti akun demo.
- Simpan versi model AI untuk reproducibility.
