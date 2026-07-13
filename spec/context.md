# Project Context: TrophyHub (Sistem Manajemen Turnamen Olahraga)

TrophyHub adalah platform manajemen turnamen olahraga (khususnya Futsal) terintegrasi yang membagi sistemnya menjadi dua bagian utama: backend panel administrasi untuk panitia/petugas turnamen dan frontend landing page interaktif untuk publik dan peserta.

---

## 1. Arsitektur & Teknologi

Proyek ini dibangun menggunakan arsitektur terpisah antara panel manajemen (backend) dan portal publik (frontend):

### A. Backend & Panel Administrasi (`manajemen-turnamen-olahraga`)
* **Framework Utama:** Laravel 11.x
* **Interaktivitas UI:** Livewire 3.x (Single-page app feel tanpa page reload penuh) dan UI components menggunakan **Flux**.
* **Database Relasional:** PostgreSQL/MySQL untuk merekam seluruh relasi data (Tim, Pemain, Pertandingan, POS, Laporan).
* **Fungsi Utama:** Menyediakan panel manajemen aman untuk admin panitia, wasit, dan kasir event. Menyediakan API publik read-only (`/api/public/...`) untuk disajikan ke frontend React.

### B. Portal Publik Frontend (`manajemen-turnamen-olahraga-fe`)
* **Framework Utama:** React + Vite + TypeScript
* **Styling:** CSS Kustom / Tailwind CSS (Frosted Glass / Sleek Dark Theme)
* **Animasi:** Motion (Framer Motion)
* **Fungsi Utama:** Halaman ramah pengunjung untuk pendaftaran tim mandiri, melihat klasemen langsung, jadwal tanding terbaru, bracket gugur, serta info galeri dokumentasi turnamen.

---

## 2. Struktur Data & Model Database

Berikut adalah model-model utama yang mendukung proses bisnis TrophyHub:

1. **`User`**: Data autentikasi petugas turnamen dengan pembagian wewenang yang diatur oleh kolom `role` (`admin`, `wasit`, `kasir`).
2. **`TournamentTeam`**: Data tim yang mendaftar turnamen. Menyimpan status validasi (`pending`, `approved`, `disqualified`), serta status pembayaran registrasi (`paid`, `unpaid`).
3. **`Player`**: Daftar roster pemain yang terdaftar di bawah suatu tim.
4. **`GameMatch`**: Data jadwal tanding yang mencakup tim bertanding, skor, waktu tanding, status (`scheduled`, `ongoing`, `done`, `cancelled`), babak/round, dan catatan pertandingan.
5. **`MatchStatistic`**: Statistik individu pemain dalam suatu pertandingan (gol, *assist*, kartu kuning, kartu merah).
6. **`Standing`**: Klasemen liga yang menghitung poin, kemenangan, kekalahan, gol, dll. secara dinamis.
7. **`PosProduct`**: Inventaris toko/kantin event turnamen (stok, harga, nama produk).
8. **`PosTransaction` & `PosTransactionDetail`**: Pencatatan transaksi POS kasir berdasarkan tipe transaksi (`registrasi` tim, `retail` kantin/merchandise, `denda` kartu/pelanggaran pemain).
9. **`Gallery`**: Dokumentasi visual turnamen dalam bentuk foto beserta deskripsi singkat.

---

## 3. Alur Komunikasi Backend & Frontend

Proyek backend mengekspos rute API publik di [api.php](file:///d:/sistem-manajemen-olahraga/manajemen-turnamen-olahraga/routes/api.php) yang dapat diakses tanpa autentikasi oleh React frontend:
* **`GET /api/public/teams`**: Menampilkan tim peserta turnamen.
* **`POST /api/public/teams/register`**: Mengirim formulir pendaftaran tim baru dari publik.
* **`GET /api/public/matches`**: Menampilkan jadwal dan hasil pertandingan ter-update.
* **`GET /api/public/standings`**: Menampilkan klasemen liga terbaru secara real-time.
* **`GET /api/public/bracket`**: Menampilkan struktur bracket bagan fase gugur (*knockout*).
