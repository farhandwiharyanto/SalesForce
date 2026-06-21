# Project Documentation & Changelog

Dokumen ini berisi rangkuman perubahan (*update*) terbaru pada sistem CRM SalesForce dan hal-hal yang perlu dipastikan pada tahap pengembangan selanjutnya.

## 📌 Ringkasan Perubahan (Update Terakhir)

### 1. Perombakan UI/UX (Layout & Desain)
- Mengubah navigasi utama dari *Top Header* menjadi **Sidebar Kiri**, sehingga tampilan lebih luas dan tidak berdesakan ketika menu bertambah.
- Merombak total desain tabel **OrderSales Logs** (sebelumnya bernama Webhooks) agar mirip dengan standar desain *enterprise* yang diinginkan (terdapat fitur checkbox, baris pencarian, dan desain drop-down/inner view bergaris abu-abu).
- Mengganti seluruh *popup* bawaan browser (`alert()` dan `confirm()`) di seluruh aplikasi menggunakan modul **SweetAlert2**, sehingga interaksi dan konfirmasi (*Convert Lead*, *Delete User*, *Sync Retry*) terlihat jauh lebih estetik dan profesional.

### 2. Penyesuaian Role-Based Access Control (RBAC)
- **Role Baru "Administrator":** Ditambahkan di menu User Management. Role ini secara akses menu disamakan dengan Sales, namun dibekali dengan **otoritas penuh untuk menghapus (Delete) data**.
- **Pencabutan Akses Delete untuk Sales:** Secara logik (*Backend*) dan visual (*Frontend*), *user* dengan role `sales` dan `pimpinan_sales` sudah tidak bisa lagi menghapus data apa pun.
- **Akses Penuh Admin:** Role `admin` yang sebelumnya *Read-Only* kini telah diubah memiliki akses mutlak (*Full Access*) untuk menambah (*Create*), mengubah (*Update*), dan menghapus (*Delete*) semua data di sistem.

### 3. Fitur "OrderSales Logs" & Sync Retry
- Modul *Webhooks* diubah namanya menjadi **OrderSales Logs** di seluruh sistem (termasuk pada navigasi *sidebar* dan hak akses *user*).
- Menambahkan fitur **"Retry Sync"** (tombol berwarna kuning) khusus untuk log sinkronisasi yang berstatus *Failed*.
- Tombol ini memungkinkan pengguna mengirimkan ulang data pesanan dari CRM ke *Project* OrderSales tanpa harus membuat entitas *Deal* yang baru.

---

## ⚠️ Tindakan Selanjutnya (Action Items to Verify)

Hal **terpenting** yang harus dipastikan sebelum sistem ini benar-benar berjalan secara *End-to-End* (*Production*):

1. **Kesiapan Server OrderSales:**
   Saat ini aplikasi SalesForce baru melakukan *Mocking* (simulasi pencatatan di *database* mandiri) ketika *Deal Won* atau *SIA Contract* dibuat. **Kita belum melakukan tembakan HTTP POST sungguhan**.
   
   👉 *Harap pastikan apakah Endpoint API pada Project OrderSales (di `https://ordersales.farhandwih.my.id/...`) sudah jadi dan siap menerima data request.*

2. **Integrasi HTTP Asli (Laravel Http Client):**
   Jika server OrderSales sudah siap, *Backend Controller* di SalesForce (`DealController` dan `SiaContractController`) harus segera kita perbarui untuk menggunakan `Http::post(...)` agar data sungguhan mengalir di jaringan.

3. **Pencocokan Payload:**
   Pastikan format *JSON Payload* yang ditembakkan oleh aplikasi ini sudah persis 100% sama dengan yang diekspektasikan (*Request Body*) oleh aplikasi OrderSales Anda.
