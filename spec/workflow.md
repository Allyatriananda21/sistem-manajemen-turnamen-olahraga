# Workflow per Role — TrophyHub Frontend

Dokumen ini menjelaskan alur kerja (workflow) setiap role yang berinteraksi dengan aplikasi **TrophyHub**, berdasarkan fitur dan halaman yang tersedia di frontend.

---

## Role yang Terlibat

1. **Pengunjung Umum (Guest / Visitor)** — Siapa saja yang membuka aplikasi tanpa aksi apapun
2. **Manajer / Perwakilan Tim** — Pengguna yang ingin mendaftarkan tim ke turnamen
3. **Penonton / Supporter** — Pengguna yang memantau perkembangan turnamen
4. **Administrator (Backend)** — Mengelola data di backend/admin panel (tidak melalui frontend ini)

---

## 1. Pengunjung Umum (Guest / Visitor)

### Tujuan
Mengenal turnamen, melihat informasi dasar, dan navigasi ke bagian yang diminati.

### Workflow

```
Buka URL Aplikasi
    │
    ▼
Halaman Beranda (/)
    ├─ Baca informasi turnamen (hero section)
    ├─ Lihat statistik: total tim, total pertandingan, laga live
    ├─ Baca format & regulasi turnamen
    ├─ Baca FAQ (accordion)
    └─ Klik Quick Access Card → navigasi ke halaman yang diinginkan
         ├─ → /teams (Tim Peserta)
         ├─ → /matches (Jadwal & Hasil)
         ├─ → /standings (Klasemen)
         └─ → /bracket (Bagan)
```

### Halaman yang Diakses
- `/` — Beranda

### Aksi yang Bisa Dilakukan
- Scroll halaman dan lihat animasi parallax
- Buka/tutup FAQ accordion
- Klik card navigasi cepat
- Klik tombol "Daftarkan Tim Kamu" → diarahkan ke `/register`
- Klik "Jelajahi Kontestan" → diarahkan ke `/teams`

---

## 2. Manajer / Perwakilan Tim

### Tujuan
Mendaftarkan tim baru ke dalam sistem turnamen.

### Workflow

```
Beranda (/)
    │
    ▼
Klik "Daftarkan Tim Kamu" atau Klik "Daftar Tim" di Navbar
    │
    ▼
Halaman Registrasi (/register)
    │
    ├─ Isi form pendaftaran:
    │    ├─ Nama Tim (wajib, maks. 100 karakter)
    │    ├─ Kategori Olahraga (dropdown: Futsal, Basket, Voli, Badminton, Sepak Bola, Lainnya)
    │    ├─ Nama Pelatih (opsional, maks. 100 karakter)
    │    ├─ Nama Penanggung Jawab / CP (wajib, maks. 100 karakter)
    │    ├─ Nomor WhatsApp / Telepon (wajib, hanya angka, maks. 20 karakter)
    │    └─ Upload Logo Tim (opsional, JPG/PNG/WEBP, maks. 2MB)
    │
    ├─ Validasi client-side dijalankan otomatis
    │    ├─ Jika ada error → tampil pesan di bawah field & alert merah
    │    └─ Jika valid → lanjut submit
    │
    ▼
Klik "Kirim Pendaftaran Tim"
    │
    ├─ Loading state (spinner + teks "Memproses...")
    │
    ├─ [GAGAL] Response error dari server
    │    ├─ 422 Validation Error → field error dimapping ke form
    │    └─ Error lain → pesan di field pertama
    │
    └─ [BERHASIL] Response 200/201 dari server
          │
          ▼
     Halaman Sukses (masih di /register)
          ├─ Tampil nama tim yang baru terdaftar
          ├─ Informasi: "Menunggu Persetujuan Panitia"
          ├─ Step berikutnya:
          │    1. Menunggu Persetujuan Panitia (admin verifikasi)
          │    2. Konfirmasi & Pembayaran (admin kirim instruksi)
          └─ Pilihan:
               ├─ Kembali ke Beranda → /
               ├─ Lihat Daftar Tim → /teams
               └─ Daftar Tim Lain → reset form, kembali ke formulir
```

### Halaman yang Diakses
- `/` → `/register`

### Data yang Dikirim ke Backend
- `name` — Nama tim
- `sport_type` — Kategori olahraga
- `coach_name` — Nama pelatih (opsional)
- `contact_person` — Nama penanggung jawab
- `phone` — Nomor telepon
- `logo` — File gambar (multipart/form-data, opsional)

### Catatan
- Status awal tim setelah daftar: **Menunggu Persetujuan Panitia**
- Status pembayaran awal: **Belum Lunas** (ditampilkan di halaman Teams)
- Verifikasi dan persetujuan dilakukan oleh admin via backend panel

---

## 3. Penonton / Supporter

### Tujuan
Memantau seluruh aspek turnamen: tim peserta, jadwal pertandingan, klasemen, dan bagan bracket.

---

### 3a. Melihat Tim Peserta

```
Klik "Tim" di Navbar atau klik card "Tim Kontestan" di Beranda
    │
    ▼
Halaman Tim (/teams)
    ├─ Lihat grid semua tim yang terdaftar (logo, nama, olahraga, status bayar)
    ├─ Gunakan search bar → filter tim by nama (client-side)
    │    ├─ Hasil ditemukan → tampil kartu tim yang cocok
    │    └─ Tidak ditemukan → tampil EmptyState "Tim Tidak Ditemukan"
    └─ Klik "Refresh Data" → reload data dari API
```

**State yang Mungkin Muncul:**
- Loading → skeleton 6 card
- Error API → ErrorState + tombol retry
- Tidak ada tim → EmptyState "Belum ada tim terdaftar"
- Tim ada tapi search tidak cocok → EmptyState "Tim Tidak Ditemukan"
- Normal → grid tim

---

### 3b. Memantau Jadwal & Hasil Pertandingan

```
Klik "Jadwal" di Navbar atau klik card "Jadwal & Hasil" di Beranda
    │
    ▼
Halaman Matches (/matches)
    │
    ├─ Jika ada laga LIVE → tampil di bagian paling atas dengan indikator merah blink
    │
    ├─ Filter berdasarkan status:
    │    ├─ Semua Laga (default)
    │    ├─ LIVE (ongoing)
    │    ├─ Terjadwal (scheduled)
    │    ├─ Selesai (done)
    │    └─ Batal (cancelled)
    │
    ├─ Filter berdasarkan babak/round (dropdown dinamis dari data)
    │
    ├─ Lihat kartu pertandingan:
    │    ├─ Nama babak, status badge
    │    ├─ Tim 1 vs Tim 2 (dengan skor jika tersedia)
    │    ├─ Tanggal & waktu tanding
    │    ├─ Venue/lokasi
    │    └─ Winner badge jika status "done"
    │
    └─ Klik "Refresh" untuk memperbarui data
```

**State yang Mungkin Muncul:**
- Loading → skeleton 4 card
- Error API → ErrorState + tombol retry
- Tidak ada pertandingan (filter aktif) → EmptyState spesifik per filter
- Normal → grid MatchCard

---

### 3c. Memantau Klasemen

```
Klik "Klasemen" di Navbar atau klik card "Klasemen Turnamen" di Beranda
    │
    ▼
Halaman Standings (/standings)
    │
    ├─ Pilih mode tampilan:
    │    ├─ Ringkas → tampil di mobile (nama + poin saja)
    │    └─ Detail / Full → tampil tabel lengkap (semua kolom)
    │
    ├─ Lihat tabel klasemen:
    │    ├─ #1 (Emas) — highlight gradient kuning + medal icon
    │    ├─ #2 (Perak) — highlight gradient abu + medal icon
    │    ├─ #3 (Perunggu) — highlight gradient amber + medal icon
    │    └─ Peringkat lainnya — tampil normal
    │    
    ├─ Kolom tersedia:
    │    Pos, Tim, Main (M), Menang (M), Seri (S), Kalah (K), Selisih Gol (SG), Poin
    │
    └─ Klik "Perbarui Klasemen" → reload data dari API
```

**State yang Mungkin Muncul:**
- Loading → skeleton tabel 8 baris
- Error API → ErrorState + tombol retry
- Belum ada data → EmptyState "Klasemen belum tersedia — menunggu pertandingan selesai"
- Normal → tabel klasemen

---

### 3d. Melihat Bagan Bracket / Sistem Gugur

```
Klik "Bracket" di Navbar atau klik card "Bagan Bracket" di Beranda
    │
    ▼
Halaman Bracket (/bracket)
    │
    ├─ [Mobile/Tablet] → Layout vertikal per babak
    │    └─ Setiap babak → grid 1-2 kolom MatchBracketCard
    │
    ├─ [Desktop] → Layout horizontal kolom per babak
    │    ├─ Connector lines antar babak (kiri & kanan card)
    │    └─ Kolom urut dari kiri: babak awal → final
    │
    ├─ Setiap MatchBracketCard menampilkan:
    │    ├─ Match ID
    │    ├─ Status badge (LIVE / SELESAI / TERJADWAL / BATAL)
    │    ├─ Tim 1 vs Tim 2 (atau BYE jika team1 == team2)
    │    ├─ Skor (jika ongoing/done)
    │    └─ Label pemenang "Lolos: [Nama Tim]" (jika done)
    │
    └─ Klik "Refresh Bagan" → reload data dari API
```

**State yang Mungkin Muncul:**
- Loading → skeleton 3 kolom, masing-masing 2 card
- Error API → ErrorState + tombol retry
- Bracket belum ada → EmptyState "Belum ada bagan eliminasi"
- Normal → bracket layout per babak

---

## 4. Administrator (Backend — Tidak via Frontend Ini)

Admin tidak menggunakan halaman frontend ini untuk pengelolaan data. Semua aksi admin dilakukan melalui backend/admin panel terpisah:

| Aksi Admin | Keterangan |
|---|---|
| Verifikasi & setujui pendaftaran tim | Review form registrasi yang masuk |
| Update status pembayaran tim | Ubah `payment_status` → `paid` |
| Input/update skor pertandingan | Perbarui `score` dan `status` match |
| Tentukan pemenang pertandingan | Set `winner_id` |
| Buat/atur jadwal pertandingan | Create/update `match_date`, `venue`, `round` |
| Generate bagan bracket | Buat data bracket setelah fase grup selesai |
| Update status pertandingan | Ubah antara `scheduled` / `ongoing` / `done` / `cancelled` |

Data yang dikelola admin akan otomatis tercermin di frontend saat user melakukan refresh atau membuka ulang halaman.

---

## Ringkasan Jalur Navigasi

```
/ (Beranda)
├─ → /register    (Daftar Tim)
├─ → /teams       (Tim Peserta)
├─ → /matches     (Jadwal & Hasil)
├─ → /standings   (Klasemen)
└─ → /bracket     (Bagan Bracket)
```

Semua halaman dapat diakses langsung dari Navbar tanpa autentikasi. Aplikasi ini sepenuhnya bersifat **publik** — tidak ada sistem login untuk pengguna biasa.
