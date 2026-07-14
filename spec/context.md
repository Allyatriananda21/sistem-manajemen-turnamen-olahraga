# Context: Sistem Manajemen Turnamen Olahraga (Frontend)

## Gambaran Umum

**TrophyHub** adalah aplikasi web frontend untuk manajemen turnamen olahraga berbasis React + TypeScript. Aplikasi ini berfungsi sebagai portal publik yang memungkinkan peserta mendaftarkan tim, serta seluruh pengguna memantau perkembangan turnamen secara real-time — mulai dari jadwal pertandingan, klasemen, hingga bagan bracket sistem gugur.

Aplikasi ini bersifat **read-heavy** untuk publik umum dan memiliki satu jalur aksi untuk user biasa, yakni pendaftaran tim. Pengelolaan data (input skor, verifikasi tim, manajemen pertandingan) diasumsikan dilakukan di sisi backend/admin panel terpisah.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | React 19 + TypeScript |
| Build Tool | Vite 6 |
| Routing | React Router DOM v7 |
| Styling | Tailwind CSS v4 |
| Animasi | Motion (framer-motion) + GSAP |
| HTTP Client | Axios |
| Icon Library | Lucide React |
| AI Integration | @google/genai (Google Gemini) |
| Server (proxy) | Express.js (Node) |

---

## Arsitektur Project

```
src/
├── App.tsx              # Root component, routing, page transitions
├── main.tsx             # React entry point
├── index.css            # Global CSS + Tailwind imports
├── config/
│   └── api.ts           # Base URL API → '/api/public'
├── hooks/
│   └── useFetch.ts      # Generic async data-fetching hook (axios)
├── pages/
│   ├── Home.tsx         # Landing page + stats dashboard
│   ├── Teams.tsx        # Daftar tim peserta + search
│   ├── Register.tsx     # Formulir pendaftaran tim baru
│   ├── Matches.tsx      # Jadwal & hasil pertandingan
│   ├── Standings.tsx    # Tabel klasemen poin
│   └── Bracket.tsx      # Bagan knockout/eliminasi
├── components/
│   ├── Navbar.tsx       # Navigasi utama + mobile drawer
│   ├── Footer.tsx       # Footer halaman
│   ├── TeamCard.tsx     # Card display data tim
│   ├── MatchCard.tsx    # Card display data pertandingan
│   ├── ElaraReveal.tsx  # Animated section (AI/feature reveal)
│   ├── EmptyState.tsx   # Komponen state kosong generik
│   ├── ErrorState.tsx   # Komponen state error generik
│   └── LoadingSkeleton.tsx # Skeleton loading cards
└── utils/
    └── formatDate.ts    # Formatter tanggal Indonesia
```

---

## Koneksi ke Backend

Semua request API diarahkan ke `/api/public` (proxy via Vite atau Express).

### Endpoint yang Digunakan

| Endpoint | Method | Fungsi |
|---|---|---|
| `/api/public/teams` | GET | Ambil semua tim peserta |
| `/api/public/teams/register` | POST | Daftarkan tim baru (multipart/form-data) |
| `/api/public/matches` | GET | Ambil semua jadwal pertandingan |
| `/api/public/standings` | GET | Ambil tabel klasemen |
| `/api/public/bracket` | GET | Ambil data bagan bracket eliminasi |

### Format Response Umum

```json
{
  "data": [ ... ]   // Array objek data
}
```

Hook `useFetch` menangani error secara otomatis dan menyediakan state `data`, `loading`, `error`, dan `refetch`.

---

## Model Data Utama

### Team
```typescript
{
  id: number;
  name: string;
  sport_type: string;       // 'Futsal' | 'Basket' | 'Voli' | 'Badminton' | 'Sepak Bola' | 'Lainnya'
  logo: string | null;      // URL logo
  payment_status: 'paid' | 'unpaid';
}
```

### Match
```typescript
{
  id: number;
  round: string;            // Nama babak, e.g. 'Babak 16 Besar', 'Semi Final'
  status: 'scheduled' | 'ongoing' | 'done' | 'cancelled';
  match_date: string;       // ISO datetime
  venue: string;
  team1: { id: number; name: string };
  team2: { id: number; name: string };
  score?: { team1: number; team2: number };
  winner_id: number | null;
}
```

### StandingItem
```typescript
{
  team_id: number;
  team_name: string;
  played: number;
  win: number;
  draw: number;
  lose: number;
  points: number;
  goal_diff: number;
}
```

### BracketRound
```typescript
{
  round: string;   // Nama babak
  matches: Match[];
}
```

---

## Halaman & Fitur

### 1. Home (`/`)
- Hero section dengan parallax scroll effect
- Counter animasi (jumlah tim, pertandingan, laga live)
- Quick navigation cards ke semua halaman
- Bagian regulasi turnamen
- FAQ accordion interaktif
- ElaraReveal section (animasi AI/intro fitur)

### 2. Teams (`/teams`)
- Grid 3 kolom tampilan kartu tim
- Client-side search berdasarkan nama tim
- Menampilkan logo, nama, kategori olahraga, dan status pembayaran
- Tombol refresh data

### 3. Register (`/register`)
- Form multi-field pendaftaran tim baru
- Upload logo dengan preview dan validasi (JPG/PNG/WEBP, max 2MB)
- Validasi client-side sebelum submit
- Pengiriman via `multipart/form-data` (POST)
- Halaman sukses berisi step berikutnya pasca pendaftaran
- Mapping error validasi backend (422 response)

### 4. Matches (`/matches`)
- Tab filter status: Semua / LIVE / Terjadwal / Selesai / Batal
- Dropdown filter babak/round
- Highlight laga LIVE di bagian paling atas
- Grid card pertandingan dengan skor, venue, tanggal
- Tombol refresh data

### 5. Standings (`/standings`)
- Tabel klasemen dengan warna podium (Emas/Perak/Perunggu)
- Mode tampilan: Ringkas (mobile) & Detail/Full (desktop)
- Kolom: Pos, Tim, Main, Menang, Seri, Kalah, Selisih Gol, Poin
- Tombol refresh data

### 6. Bracket (`/bracket`)
- Layout desktop: Kolom horizontal per babak dengan connector lines
- Layout mobile: Blok vertikal per babak
- Card per pertandingan menampilkan tim, skor, status
- Penanganan BYE (tim otomatis lolos)
- Highlight pemenang dengan indikator visual

---

## Komponen Shared

- **Navbar**: Fixed top nav, berubah style saat scroll, mobile drawer menu
- **Footer**: Footer generik
- **TeamCard**: Card visual tim dengan logo/fallback avatar + badge status pembayaran
- **MatchCard**: Card visual pertandingan dengan skor, venue, tanggal, badge status
- **LoadingSkeleton**: Skeleton card untuk loading state
- **EmptyState**: State kosong generik dengan icon, judul, deskripsi
- **ErrorState**: State error dengan pesan dan tombol retry
- **ElaraReveal**: Section animasi reveal berbasis scroll/GSAP

---

## Pola & Konvensi

- Semua halaman menggunakan `useFetch` untuk data fetching
- State loading/error/empty ditangani secara konsisten di setiap halaman
- Animasi halaman menggunakan `AnimatePresence + motion.div` (page transitions)
- Komponen menggunakan `motion.div` dari `motion/react` (framer-motion v12+)
- Design system: dark navy (#0F172A), accent lime (#E4FD97), glass-card pattern
- Bahasa UI: **Bahasa Indonesia** sepenuhnya
- Brand name: **TrophyHub**
- Semua API route prefix: `/api/public`

---

## Konfigurasi Build

- `vite.config.ts` — Vite + React + Tailwind CSS plugin
- `tsconfig.json` — TypeScript strict mode
- `.env` / `.env.example` — Environment variables (tidak di-expose di sini)
- `metadata.json` — Metadata project tambahan
