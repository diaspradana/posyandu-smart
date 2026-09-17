# Installation Guide — Posyandu Smart

## A. Prasyarat

Pastikan software berikut tersedia:

| Software | Minimum | Fungsi |
|---|---:|---|
| PHP | 8.2 | Menjalankan Laravel 12 |
| Composer | 2.x | Dependency PHP |
| Node.js | LTS | Asset frontend/Vite |
| npm | mengikuti Node.js | Dependency frontend |
| MySQL | 8.x | Database aplikasi |
| Python | 3.10+ | AI microservice |
| Git | terbaru | Version control |

## B. Clone Repository

```bash
git clone <URL-REPOSITORY>
cd posyandu-smart
```

## C. Backend Laravel

Install dependency:

```bash
composer install
```

Buat environment:

```bash
copy .env.example .env
```

Linux/macOS:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

## D. Konfigurasi MySQL

Buat database:

```sql
CREATE DATABASE posyandu_smart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atur `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=posyandu_smart
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username/password dengan instalasi MySQL lokal.

## E. Migration dan Seeder

```bash
php artisan migrate --seed
```

Jika ingin mengulang database development dari awal:

```bash
php artisan migrate:fresh --seed
```

> `migrate:fresh` menghapus seluruh tabel. Jangan gunakan pada database produksi.

## F. Frontend

```bash
npm install
npm run build
```

Untuk development dengan hot reload:

```bash
npm run dev
```

## G. Jalankan Laravel

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

## H. Menjalankan AI Service

Masuk ke folder AI:

```bash
cd ai_service
python -m venv .venv
```

Windows:

```bash
.venv\Scripts\activate
```

Linux/macOS:

```bash
source .venv/bin/activate
```

Install dependency:

```bash
pip install -r requirements.txt
```

Jalankan FastAPI:

```bash
uvicorn main:app --host 0.0.0.0 --port 8001
```

Health check:

```text
http://127.0.0.1:8001/health
```

## I. Konfigurasi Laravel → AI Service

Tambahkan URL service pada `.env`:

```env
AI_SERVICE_URL=http://127.0.0.1:8001
```

Pastikan konfigurasi service Laravel membaca variabel tersebut sesuai implementasi `config/services.php` dan `AiPredictionService`.

## J. Akun Demo

Seeder membuat akun berikut:

- Admin: `admin@posyandusmart.test` / `password123`
- Kader Melati: `kader@posyandusmart.test` / `password123`
- Kader Melati: `kader.melati@posyandusmart.test` / `password123`
- Kader Mawar: `kader.mawar@posyandusmart.test` / `password123`
- Kader Kenanga: `kader.kenanga@posyandusmart.test` / `password123`
- Kader Dahlia: `kader.dahlia@posyandusmart.test` / `password123`

Akun tersebut ditujukan untuk demo/development.

## K. Pengujian

Jalankan:

```bash
php artisan test
```

Jika ingin memeriksa route:

```bash
php artisan route:list
```

## L. Training Ulang Model AI

Maternal:

```bash
cd ai_service
python train_maternal.py
```

Balita:

```bash
cd ai_service
python train_balita.py
```

Script training menghasilkan file model `.pkl` dan metadata `.json` pada `ai_service/models`.

## M. Troubleshooting

### `SQLSTATE[HY000] [1045]`
Periksa `DB_USERNAME` dan `DB_PASSWORD` pada `.env`.

### `Unknown database 'posyandu_smart'`
Buat database terlebih dahulu atau ubah `DB_DATABASE`.

### `Vite manifest not found`
Jalankan:

```bash
npm install
npm run build
```

### AI service tidak merespons
Pastikan FastAPI berjalan pada port 8001 dan nilai `AI_SERVICE_URL` benar.

### Model AI tidak ditemukan
Pastikan file berikut tersedia:

```text
ai_service/models/maternal_model.pkl
ai_service/models/maternal_metadata.json
ai_service/models/stunting_model.pkl
ai_service/models/stunting_metadata.json
```
