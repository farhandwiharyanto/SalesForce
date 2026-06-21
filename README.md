# 🏢 SalesForce (CRM & Pre-Sales Application)

Proyek ini adalah sistem **Customer Relationship Management (CRM) & Pre-Sales** yang dirancang khusus untuk memfasilitasi manajemen prospek (*Leads*), manajemen kontak (*Contacts*), serta manajemen transaksi & kesepakatan (*Deals/Opportunities*).

Aplikasi ini juga telah diatur untuk beroperasi sebagai ujung tombak (hulu) dari *pipeline* integrasi data perusahaan, secara otomatis mengirimkan data transaksi yang berhasil (*Closed Won*) ke sistem **OrderSales** dan **AfterSales**.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** Vue.js 3 + Composition API + Tailwind CSS + Pinia
- **Database:** PostgreSQL 16
- **Infrastruktur:** Docker & Docker Compose (Containerized)
- **Autentikasi:** Laravel Sanctum (Token-based Authentication)

---

## 🌟 Fitur Utama

### 1. Role-Based Access Control (RBAC)
Sistem memiliki 3 level hak akses pengguna:
- **Admin (`admin@salesforce.com`):** Memiliki akses pantau (Read-Only) untuk seluruh data di dalam sistem tanpa kemampuan untuk memodifikasi transaksi.
- **Pimpinan Sales (`pimpinan@salesforce.com`):** Mampu mengelola transaksi, dan memiliki hak eksklusif untuk menyetujui (**Approve**) atau menolak (**Reject**) pengajuan diskon.
- **Sales (`sales@salesforce.com`):** Agen di lapangan yang bertugas mengelola siklus transaksi (*stage*) dan mengajukan diskon (**Request Discount**). Sistem akan mengunci kemampuan *Sales* untuk menutup transaksi (menjadi *Closed Won*) apabila status diskon masih *Pending*.

*(Catatan: Password untuk semua akun default adalah `password123`)*

### 2. Manajemen Modul Data
- **Deals (Opportunities):** Mengelola siklus transaksi dari *Prospecting* hingga *Closed Won / Closed Lost*.
- **Customers:** Modul direktori pelanggan B2B yang menyimpan *Nomor SIA*, *Nomor Customer*, dan *Customer Name*. Tersedia endpoint khusus untuk sinkronisasi dengan aplikasi eksternal.
- **Leads, Contacts, & Products:** Manajemen data dasar (Master Data) untuk operasional sales.

### 3. Pipeline Integrasi Otomatis (Webhook Publisher)
Sistem ini bertindak sebagai *trigger* utama bagi aplikasi operasional lainnya di perusahaan.
Ketika sebuah transaksi (Deal) diubah statusnya menjadi **`closed_won`**, sistem secara otomatis di *background* (*Queue/Job*) akan mengirimkan **HTTP POST (Webhook)** ke *project* OrderSales.

**Format Payload Integrasi (Sesuai Standar Arsitektur):**
```json
{
  "event": "crm.deal_won",
  "deal_info": {
    "opp_id": "OPP-771",
    "gross_amt": 15000000,
    "customer": "Budi Santoso",
    "email": "budi@example.com",
    "product_id": "PROD-1"
  },
  "CRRM": {
    "nem_field": "metadata_crm_ok",
    "Nomor SIA": "SIA-9988-77"
  }
}
```
*Autentikasi Webhook menggunakan HTTP Header: `Authorization: Bearer mock-jwt-token`.*

### 4. Endpoint Integrasi Master Data (REST API)
Aplikasi OrderSales dapat melakukan penarikan data (*Data Pull*) *Customer* secara berkala melalui endpoint:
`GET /api/integration/ordersales/customers`

### 5. Dokumentasi Perubahan Terkini
Berbagai penyesuaian terbaru terkait **UI Sidebar**, **RBAC Baru (Administrator)**, fitur sinkronisasi ulang (**OrderSales Logs & Retry Sync**), hingga pergantian *popup* menjadi **SweetAlert2** dapat Anda lihat selengkapnya pada file [CHANGELOG.md](file:///Users/farhandwiharyanto/SalesForce/CHANGELOG.md). File ini juga memuat *Action Items* penting yang perlu dicek sebelum merilis aplikasi ke tahap integrasi API riil.

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Karena seluruh aplikasi telah dibungkus menggunakan Docker, menjalankannya sangatlah mudah.

1. **Pastikan Docker Desktop sudah menyala.**
2. Buka terminal dan arahkan ke direktori proyek ini:
   ```bash
   cd /path/to/SalesForce
   ```
3. **Nyalakan semua kontainer:**
   ```bash
   docker compose up -d
   ```
4. **Jalankan Migrasi & Seeder Database:** (Ini akan membuat tabel dan akun dummy).
   ```bash
   docker exec crm-backend php artisan migrate:fresh --seed
   ```
5. **Akses Aplikasi:**
   - **Frontend (UI CRM):** Buka `http://localhost:5173` di browser.
   - **Backend API:** Berjalan di `http://localhost:8000`.

---

<br>

<div align="center">
  <b>Dibuat untuk Simulasi Integrasi Sistem Enterprise (CRM ➡ Order ➡ AfterSales)</b><br>
  Created By: Farhan Dwi Haryanto ❤️
</div>
