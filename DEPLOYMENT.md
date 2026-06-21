# Panduan Deployment & Workflow: Vercel dan PostgreSQL (Neon)

Dokumen ini berisi panduan *step-by-step* untuk arsitektur deployment aplikasi OrderSales (SalesForce CRM) yang telah kita bangun, menggunakan arsitektur 100% Serverless di Vercel.

- **Frontend (Vue.js/Vite)** di **Vercel**
- **Backend (Laravel 11 PHP)** di **Vercel** (menggunakan `vercel-php`)
- **Database (PostgreSQL)** di **Neon.tech**

---

## 1. Arsitektur Branching & Workflow Development

Proyek ini menggunakan dua branch utama untuk memisahkan environment lokal dan production:

1. **`development` branch:**
   - Digunakan untuk ngoding, eksperimen, dan penambahan fitur baru di komputer lokal.
   - Menggunakan database PostgreSQL lokal via Docker (`DB_HOST=127.0.0.1`, `DB_PORT=54320`).
   - File `.env` lokal akan otomatis menggunakan settingan ini (karena `.env` masuk ke `.gitignore` sehingga aman tidak ter-upload).

2. **`main` branch:**
   - Digunakan khusus untuk **Production**.
   - Ketika kode di branch `development` sudah stabil, lakukan merge ke `main` dan push ke GitHub.
   - Vercel akan secara otomatis mendeteksi perubahan di branch `main` dan melakukan redeploy.

---

## 2. Deploy Backend (Laravel 11) ke Vercel

Backend Laravel ini telah dimodifikasi agar dapat berjalan di lingkungan Vercel Serverless Functions yang sifatnya *read-only*.

### Konfigurasi Khusus Serverless:
- Direktori storage dan cache (`storage/framework/views`, `cache`, `sessions`) dialihkan ke direktori writable `/tmp` pada saat runtime Vercel melalui modifikasi di `bootstrap/app.php`.
- Logging dialihkan ke `stderr` (`LOG_CHANNEL=stderr`) agar log aplikasi langsung terbaca di tab Logs Vercel.

### Langkah Setup Environment Variables di Vercel (Backend):
Pastikan Environment Variables berikut diatur di Vercel Dashboard project backend:
- `APP_ENV`: `production`
- `APP_DEBUG`: `false`
- `APP_KEY`: *(Gunakan base64 app key dari lokal)*
- `DB_CONNECTION`: `pgsql`
- `DB_HOST`: *(Host endpoint pooler Neon.tech)*
- `DB_PORT`: `5432`
- `DB_DATABASE`: `neondb`
- `DB_USERNAME`: `neondb_owner`
- `DB_PASSWORD`: *(Password database Neon)*
- `DB_SSLMODE`: `require;options=endpoint=ep-nama-endpoint-kamu` **(PENTING: Gunakan ini untuk bypass isu SNI libpq lama pada AWS Lambda)**
- `SESSION_DRIVER`: `cookie` atau `database`
- `FRONTEND_URL`: `https://sales-force-indol.vercel.app` (Untuk keperluan CORS)

---

## 3. Deploy Frontend (Vue.js) ke Vercel

Frontend di-deploy sebagai proyek terpisah di Vercel agar dapat memanfaatkan CDN global Vercel secara maksimal.

### Langkah Setup Environment Variables di Vercel (Frontend):
Untuk memastikan Frontend (Vue) dapat berkomunikasi dengan Backend (Laravel), satu variabel environment krusial harus ditambahkan:
- `VITE_API_URL`: `https://sales-force-backend.vercel.app/api`

**(Penting):** Jika variabel `VITE_API_URL` baru ditambahkan atau diubah, Vercel frontend **harus di-redeploy** agar variabel tersebut di-inject ke build statis Vue.js.

---

## 4. Penanganan Error Umum

### 500 Server Error (Backend)
Jika backend mereturn error 500 (atau "Server Error" di halaman depan), kemungkinan besar gagal koneksi database akibat masalah SNI (Server Name Indication) dari versi `libpq` yang sudah lawas di environment Amazon Linux 2 (mesin di balik Vercel). 
**Solusi:** Selalu tambahkan `;options=endpoint=<id-endpoint-neon>` di akhir variabel `DB_SSLMODE` pada dashboard Vercel.

### Invalid username or password (Frontend)
Jika kredensial sudah dipastikan benar namun frontend masih menampilkan error ini (khususnya setelah deploy), periksa variabel `VITE_API_URL` di Vercel Dashboard (Frontend). Jika variabel tersebut mengarah ke `http://localhost:8000/api`, maka browser gagal melakukan koneksi API dan akan jatuh ke fallback catch error "Invalid username or password".
