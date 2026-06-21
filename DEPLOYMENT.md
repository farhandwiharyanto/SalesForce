# Panduan Deployment Gratis: Vercel, Render, dan PostgreSQL

Dokumen ini berisi panduan *step-by-step* untuk mendeploy aplikasi OrderSales dengan menggunakan stack gratis yang populer:
- **Frontend (Vue.js)** di **Vercel**
- **Backend (Laravel + Docker)** di **Render**
- **Database (PostgreSQL)** di **Supabase / Neon.tech**

---

## 1. Siapkan Database Gratis (PostgreSQL)

Karena aplikasi membutuhkan database PostgreSQL, disarankan menggunakan layanan gratis yang sifatnya permanen (bukan trial 90 hari):
- [Supabase](https://supabase.com/)
- [Neon.tech](https://neon.tech/)

**Langkah-langkah:**
1. Daftar ke salah satu layanan di atas dan buat project/database PostgreSQL.
2. Catat *Connection URL* atau detail koneksinya:
   - Host / Endpoint
   - Port (biasanya `5432`)
   - DB Name
   - User
   - Password

---

## 2. Deploy Backend (Laravel + Docker) ke Render

Render akan secara otomatis membaca file `backend/Dockerfile` dan mem-build environment Laravel.

**Langkah-langkah:**
1. Pastikan seluruh kodemu sudah di-push ke GitHub.
2. Login ke [Render.com](https://render.com/).
3. Klik tombol **New +** dan pilih **Web Service**.
4. Hubungkan ke repository GitHub kamu.
5. Pada halaman konfigurasi, isi data berikut:
   - **Name**: `ordersales-backend` (bebas)
   - **Root Directory**: `backend` *(Sangat penting supaya Render menemukan Dockerfile)*
   - **Environment**: `Docker`
   - **Instance Type**: `Free`
6. Gulir ke bawah ke bagian **Environment Variables** (Advanced) dan tambahkan env variables berikut:
   - `APP_KEY`: *(Isi dengan key dari `.env` local kamu, misal `base64:xxx`)*
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `DB_CONNECTION`: `pgsql`
   - `DB_HOST`: *(Dari database langkah 1)*
   - `DB_PORT`: `5432`
   - `DB_DATABASE`: *(Dari database langkah 1)*
   - `DB_USERNAME`: *(Dari database langkah 1)*
   - `DB_PASSWORD`: *(Dari database langkah 1)*
7. Klik **Create Web Service** dan tunggu proses build selesai.
8. Setelah berhasil, kamu akan mendapatkan **URL Backend** (misal: `https://ordersales-backend.onrender.com`). Catat URL ini!
9. *Opsional*: Jika perlu menjalankan migrasi, gunakan fitur "Shell" di dashboard Render dan ketik:
   `php artisan migrate --force`

---

## 3. Deploy Frontend (Vue.js) ke Vercel

Vercel sangat optimal untuk mendeploy project berbasis Vite/Vue.js.

**Langkah-langkah:**
1. Login ke [Vercel.com](https://vercel.com) menggunakan akun GitHub.
2. Klik **Add New Project** dan pilih repository GitHub kamu.
3. Pada bagian konfigurasi project:
   - **Framework Preset**: Pilih **Vite** atau **Vue.js**.
   - **Root Directory**: Klik tulisan *Edit* dan pilih folder `frontend`.
4. Buka bagian **Environment Variables** dan tambahkan URL Backend kamu agar Vue tahu ke mana harus melakukan request API.
   - **Name**: `VITE_API_URL` (Sesuaikan dengan nama variabel di kodemu)
   - **Value**: `https://ordersales-backend.onrender.com` *(URL backend dari langkah 2)*
5. Klik **Deploy** dan tunggu proses selesai.
6. Kamu akan mendapatkan **URL Frontend** (misal: `https://ordersales.vercel.app`).

---

## 4. Konfigurasi CORS (Langkah Krusial)

Agar Vercel (Frontend) bisa berkomunikasi dengan Render (Backend) tanpa diblokir oleh browser, kamu harus mengatur CORS (Cross-Origin Resource Sharing).

**Langkah-langkah:**
1. Kembali ke Dashboard Render (Backend).
2. Buka bagian **Environment Variables**.
3. Tambahkan variabel baru:
   - **Name**: `FRONTEND_URL`
   - **Value**: `https://ordersales.vercel.app` *(Ganti dengan URL Vercel kamu, tanpa slash di akhir)*
4. Pastikan file konfigurasi CORS Laravel (biasanya di `config/cors.php` atau `bootstrap/app.php`) menggunakan variabel `FRONTEND_URL` ini di bagian `allowed_origins`.
5. Render akan melakukan restart otomatis setelah kamu menyimpan Environment Variables baru.
