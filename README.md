# Sistem Manajemen Turnamen Olahraga

Sistem Manajemen Turnamen Olahraga adalah platform digital terintegrasi yang dirancang untuk mengelola seluruh siklus hidup sebuah kompetisi olahraga — mulai dari pendaftaran tim, pembuatan jadwal pertandingan, pencatatan skor real-time, kalkulasi klasemen otomatis, hingga sistem kasir (Point of Sale/POS) di lokasi acara. 

Sistem ini memisahkan antara bagian publik (Landing Page berbasis React.js) yang mengonsumsi data via API read-only, dan bagian operasional panitia (Back-Office berbasis Laravel & Livewire v4) untuk mengelola data turnamen secara reaktif, aman, dan real-time.

---

## 🚀 Panduan Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menyiapkan proyek di lingkungan lokal Anda:

### 1. Kloning Repositori
```bash
git clone <repository-url>
cd manajemen-turnamen-olahraga
```

### 2. Instal Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Lingkungan (Environment File)
Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database Anda (misalnya MySQL atau SQLite):
```bash
copy .env.example .env
```
*Catatan: Secara default, proyek dikonfigurasi untuk database MySQL bernama `db_olahraga`.*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database
Buat tabel-tabel database yang diperlukan dengan menjalankan perintah migrasi:
```bash
php artisan migrate
```

### 6. Isi Data Awal (Seeding)
Isi database dengan data pengguna default, tim olahraga simulasi, dan produk POS awal:
```bash
php artisan db:seed
```

### 7. Instal Dependensi Frontend & Jalankan Aset
Proyek ini menggunakan Vite untuk bundling aset. Siapkan dengan menjalankan:
```bash
npm install
npm run build
```
Untuk mode pengembangan aktif:
```bash
npm run dev
```

---

## 👥 Pengguna Default & Kredensial Login

Aplikasi ini menggunakan otentikasi berbasis *role* untuk membatasi hak akses halaman admin. Gunakan akun simulasi berikut untuk masuk ke dashboard admin:

| Peran (Role) | Nama Pengguna | Email | Password | Hak Akses Utama |
|---|---|---|---|---|
| **Admin** | Admin Panitia | `admin@turnamen.test` | `password` | Akses penuh dashboard, verifikasi tim, generate jadwal, kelola produk POS, manajemen user. |
| **Wasit** | Wasit Lapangan | `wasit@turnamen.test` | `password` | Menginput skor secara real-time, memasukkan catatan/sanksi pertandingan, menginput statistik pemain. |
| **Kasir** | Kasir Event | `kasir@turnamen.test` | `password` | Memproses pembayaran registrasi tim (invoice) & penjualan retail kantin/merchandise di lokasi. |

---

## 🛠️ Ringkasan Fitur per Modul

Sistem Manajemen Turnamen Olahraga terbagi menjadi beberapa modul operasional reaktif:

### 1. Modul Manajemen Tim
* **Pendaftaran Tim:** Formulir publik mandiri untuk perwakilan tim (input data pelatih, kontak, upload logo).
* **Verifikasi & Status:** Panitia mengelola status pendaftaran tim (`pending` $\rightarrow$ `approved`/`disqualified`).
* **Invoice Otomatis:** Sistem secara otomatis menerbitkan kode invoice unik (format: `INV-{tahun}-{id_tim}`) saat tim disetujui untuk pertama kalinya.

### 2. Modul Manajemen Jadwal (Fixtures Generator)
* **Round-Robin Generator:** Mengotomatiskan pembuatan jadwal pertandingan liga (semua tim bertemu satu sama lain) untuk tim yang berstatus `approved`.
* **Knockout Bracket Generator:** Membuat bagan eliminasi/gugur dari babak penyisihan hingga babak final.
* **Pengaturan Venue & Wasit:** Penentuan lokasi lapangan, jadwal tanggal/waktu, dan penugasan wasit.

### 3. Modul Live Score & Hasil Pertandingan
* **Alur Status Reaktif:** Progress status pertandingan linear (`scheduled` $\rightarrow$ `ongoing` $\rightarrow$ `done` atau `cancelled`).
* **Input Skor Real-time:** Pengisian skor pertandingan reaktif yang dapat diubah kapan saja selama statusnya `ongoing`.
* **Catatan & Sanksi Wasit:** Input catatan kartu merah/kuning, denda pelanggaran, atau sanksi administratif yang terhubung ke modul tagihan POS.

### 4. Modul Klasemen Otomatis
* **Kalkulasi Poin Otomatis:** Perhitungan poin otomatis saat pertandingan selesai (`done`): Menang = 3 poin, Seri = 1 poin, Kalah = 0 poin.
* **Update Tabel Standings:** Agregasi jumlah bermain (*played*), menang (*win*), seri (*draw*), kalah (*lose*), selisih gol (*goal difference*), dan poin total secara otomatis.

### 5. Modul Point of Sale (POS) Kasir Event
* **Transaksi Retail:** Kasir dapat menambahkan produk retail (merchandise/makanan) ke keranjang reaktif dengan penghitungan subtotal dan kembalian otomatis.
* **Validasi Stok Server-Side:** Validasi aman & transaksional di backend (menggunakan database locking) untuk memastikan kuantitas checkout tidak melebihi stok yang tersedia.
* **Pelunasan Registrasi & Denda:** Konfirmasi pembayaran invoice registrasi tim secara langsung di kasir yang otomatis memperbarui status bayar tim menjadi `paid`.

### 6. Modul Dashboard & Widget Ringkasan
* **Competition Overview:** Ringkasan tipe turnamen, jumlah pool, status keaktifan turnamen, dan putaran terkini.
* **Grafik Finansial:** Statistik rekapitulasi pendapatan kasir yang membedakan tipe transaksi registrasi vs retail.
* **Statistik Partisipasi:** Informasi ringkas jumlah tim terdaftar, pertandingan selesai, dan pertandingan tersisa dengan dukungan filter multi-dimensi.
