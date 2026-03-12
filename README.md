# ✈️ AirNav Indonesia — Dashboard Aktualisasi Operasional

> Dashboard analitik operasional berbasis web untuk Unit Pelayanan navigasi Penerbangan AirNav Indonesia, dibangun dengan **Laravel 12** dan **Chart.js**. Sistem ini memproses data pergerakan penerbangan harian dari file Excel operasional dan menyajikannya dalam visualisasi interaktif yang komprehensif.

---

## 📑 Daftar Isi

- [Ringkasan Sistem](#-ringkasan-sistem)
- [Arsitektur Teknologi](#-arsitektur-teknologi)
- [Struktur Database](#-struktur-database)
- [Halaman & Fitur](#-halaman--fitur)
- [API Routes](#-api-routes)
- [Alur Data (Data Pipeline)](#-alur-data-data-pipeline)
- [Struktur File Proyek](#-struktur-file-proyek)
- [Instalasi & Setup](#-instalasi--setup)
- [Konfigurasi Lingkungan](#-konfigurasi-lingkungan)
- [Penggunaan](#-penggunaan)
- [Dependensi](#-dependensi)

---

## 🔍 Ringkasan Sistem

Dashboard ini dirancang untuk membantu Tim Operasional AirNav Indonesia memantau dan menganalisis data pergerakan penerbangan di bandara-bandara yang dikelola. Sistem ini menyediakan:

- **Visualisasi tren** pergerakan penerbangan harian, bulanan, dan tahunan
- **Heatmap kalender** yang menampilkan intensitas aktivitas penerbangan sepanjang tahun
- **Drill-down interaktif** untuk analisis mendalam per hari, per jam, dan per event
- **Manajemen event** (Nataru, Lebaran, dll.) dengan overlay pada grafik tren
- **KPI cards** dengan perbandingan periode kustom
- **Export laporan** ke format Excel dan PDF
- **Catatan harian** (daily notes) yang dapat ditambahkan per tanggal pada heatmap
- **Multi-cabang** — mendukung beberapa bandara/unit cabang sekaligus

---

## 🏗 Arsitektur Teknologi

| Layer | Teknologi |
|---|---|
| **Framework** | Laravel 12.x (PHP 8.2+) |
| **Frontend** | Blade Templates, Vanilla JavaScript, Tailwind CSS (CDN) |
| **Charting** | Chart.js (dengan plugins: Annotation, DataLabels, Zoom) |
| **Spreadsheet** | PhpOffice/PhpSpreadsheet 1.30+ |
| **PDF Export** | Barryvdh/Laravel-DomPDF 3.1+ |
| **Database** | MySQL |
| **Dev Server** | Laragon / php artisan serve |
| **Build Tool** | Vite |

### Arsitektur Aplikasi

```
┌─────────────────────────────────────────────────────────────┐
│                      BROWSER (Client)                       │
│  ┌──────────┐  ┌──────────┐  ┌───────────┐  ┌───────────┐  │
│  │   Home   │  │Dashboard │  │  Upload   │  │  Summary  │  │
│  │ (Stats)  │  │(Charts + │  │  (Excel   │  │ (Tabel +  │  │
│  │          │  │ Heatmap) │  │  Parser)  │  │  Export)  │  │
│  └──────────┘  └──────────┘  └───────────┘  └───────────┘  │
│         │            │             │              │         │
│         ▼            ▼             ▼              ▼         │
│  ┌─────────────────────────────────────────────────────┐    │
│  │            Chart.js + Vanilla JavaScript            │    │
│  │   (dashboard.js — 1800+ lines, client-side logic)   │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
                            │ HTTP
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    LARAVEL 12 (Server)                      │
│                                                             │
│  Controllers:                                               │
│  ┌──────────────┐ ┌────────────────────┐ ┌──────────────┐   │
│  │HomeController│ │DashboardController │ │UploadController│  │
│  │   (149 LOC)  │ │    (701 LOC)       │ │  (282 LOC)   │   │
│  └──────────────┘ └────────────────────┘ └──────────────┘   │
│  ┌────────────────┐ ┌──────────────────┐ ┌──────────────┐   │
│  │SummaryController│ │CabangController │ │EventController│  │
│  │   (310 LOC)    │ │    (53 LOC)      │ │   (32 LOC)   │   │
│  └────────────────┘ └──────────────────┘ └──────────────┘   │
│                                                             │
│  Services:                                                  │
│  ┌─────────────────────────────────────────┐                │
│  │ DashboardService (273 LOC)              │                │
│  │ - Hourly profiles, KPI stats, Events,   │                │
│  │   Chart data prep, Yearly comparison    │                │
│  └─────────────────────────────────────────┘                │
│                                                             │
│  Models:                                                    │
│  ┌────────┐ ┌───────────────┐ ┌─────────┐ ┌─────┐ ┌─────┐  │
│  │ Cabang │ │DailyFlightStat│ │DailyNote│ │Event│ │Raw  │  │
│  │        │ │               │ │         │ │     │ │Flight│ │
│  └────────┘ └───────────────┘ └─────────┘ └─────┘ └─────┘  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
                    ┌──────────────┐
                    │    MySQL     │
                    │  (5 Tables)  │
                    └──────────────┘
```

---

## 🗄 Struktur Database

### 1. `daily_flight_stats` — Data Statistik Penerbangan Harian

Tabel utama yang menyimpan agregasi data penerbangan per hari, per cabang.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `date` | date | Tanggal data (UNIQUE bersama `branch_code`) |
| `branch_code` | varchar(10) | Kode cabang bandara (misal: WARR) |
| `total_dep` | int | Total departure |
| `total_arr` | int | Total arrival |
| `total_flights` | int | Total keseluruhan penerbangan |
| `dom_dep`, `dom_arr` | int | Departure/arrival domestik |
| `int_dep`, `int_arr` | int | Departure/arrival internasional |
| `training_dep`, `training_arr` | int | Departure/arrival training |
| `peak_hour` | time | Jam tersibuk hari itu |
| `peak_hour_count` | int | Jumlah pergerakan di jam tersibuk |
| `runway_capacity` | int | Kapasitas runway (per konfigurasi upload) |

**Index:** `UNIQUE(date, branch_code)`, `INDEX(date)`

### 2. `raw_flight_datas` — Data Hourly Mentah

Menyimpan jumlah pergerakan per jam (24 kolom) untuk setiap hari, digunakan untuk drill-down profil jam sibuk.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `date` | date | Tanggal data |
| `kode_cabang` | varchar | Kode cabang |
| `h00` — `h23` | int | Jumlah pergerakan per jam (UTC) |

**Index:** `UNIQUE(date, kode_cabang)`

### 3. `events` — Event Operasional

Menyimpan event/momen operasional (Nataru, Lebaran, dll.) yang akan di-overlay pada grafik tren.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `name` | varchar | Nama event |
| `start_date` | date | Tanggal mulai |
| `end_date` | date | Tanggal berakhir |
| `color` | varchar(7) | Warna hex untuk highlight |
| `description` | text (nullable) | Deskripsi event |

### 4. `daily_notes` — Catatan Harian

Catatan/anotasi yang ditambahkan user pada tanggal tertentu melalui heatmap drill-down.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `date` | date | Tanggal catatan |
| `branch_code` | varchar(10) | Kode cabang |
| `note` | text (nullable) | Isi catatan |

**Index:** `UNIQUE(date, branch_code)`

### 5. `cabangs` — Master Data Cabang

Data bandara/cabang yang dikelola. Seeded dengan 8 cabang default.

| Kolom | Tipe | Deskripsi |
|---|---|---|
| `id` | bigint PK | Auto-increment |
| `kode_cabang` | varchar UNIQUE | Kode ICAO cabang (misal: WARR) |
| `nama` | varchar | Nama cabang |

**Cabang Default:** Surabaya (WARR), Banyuwangi (WADY), Malang (WARA), Blora (WARC), Kediri (WARD), Jember (WARE), Sumenep (WART), Bawean (WARW)

---

## 📄 Halaman & Fitur

### 1. 🏠 Home (`/`)
**Controller:** `HomeController`
**View:** `home.blade.php`

Halaman landing yang menampilkan ringkasan statistik operasional bulan terakhir:
- Total pergerakan bulan ini + growth vs bulan sebelumnya
- Persentase domestik vs internasional
- Jam puncak tersibuk
- Rata-rata harian
- Rekor penerbangan (hari tersibuk)
- Status stress level runway
- Filter cabang

### 2. 📊 Dashboard (`/dashboard`)
**Controller:** `DashboardController` (701 LOC) + `DashboardService` (273 LOC)
**View:** `dashboard.blade.php` (85KB)
**JS:** `dashboard.js` (1800+ LOC)

Halaman analitik utama. Terdiri dari beberapa section:

#### a. Filter Bar
- Filter Tahun, Bulan, Cabang
- Filter tags interaktif (bisa dihapus per-item)
- Tombol reset filter

#### b. Insight Cards
- Kategori dominan (domestik/internasional/training)
- Perbandingan weekend vs weekday
- Rasio arrival vs departure

#### c. KPI Cards (4 buah)
- **Total Pergerakan** — dengan growth % vs periode pembanding
- **Rata-rata/Hari** — average daily flights
- **Puncak Harian** — rekor pergerakan terbanyak dalam sehari
- **Puncak Jam** — jam dengan pergerakan tertinggi
- Mendukung **perbandingan periode kustom** (custom KPI comparison)

#### d. Tren Pergerakan (Line Chart)
- Grafik tren dengan toggle: Total, Domestik, Internasional, Training
- **Event annotations** — overlay area berwarna untuk event (klik untuk detail)
- **Event drilldown modal** — profil per jam, komposisi tipe, distribusi waktu
- Mendukung zoom dan pan

#### e. Arr vs Dep (Line Chart)
- Perbandingan arrival vs departure
- Toggle: All, Arr, Dep

#### f. Pola Mingguan (Bar Chart)
- Rata-rata pergerakan per hari dalam seminggu (Senin—Minggu)
- **Bar drilldown** — klik bar untuk detail profil jam hari tersebut

#### g. Heatmap Kesibukan (Calendar Heatmap)
- Tampilan kalender 12-bulan penuh per tahun
- Warna gradasi intensitas (sepi → sibuk)
- Filter cabang dan tahun independen
- **Dot indicator** kuning pada tanggal yang memiliki catatan
- **Panel "Catatan"** — lihat semua catatan harian + navigasi langsung
- **Hapus catatan** dari panel catatan
- **Cell drilldown modal** — klik tanggal untuk detail:
  - Profil per jam (line chart)
  - Proporsi tipe (donut chart)
  - Distribusi waktu (Pagi/Siang/Malam/Dini Hari) dengan progress bars
  - 3 jam tersibuk
  - Input catatan khusus per tanggal

#### h. Komparasi Seasonality Multi-Year (Line Chart)
- Overlay pergerakan bulanan dari beberapa tahun
- Zoom/pan support

#### i. Manajemen Event
- Tombol "+ Event" untuk menambah event baru
- Modal form: nama, tanggal mulai/selesai, warna, deskripsi
- Hapus event dengan konfirmasi modal
- Event akan otomatis muncul sebagai overlay pada area grafik tren

### 3. 📤 Upload (`/upload`)
**Controller:** `UploadController` (282 LOC)
**View:** `upload.blade.php`

Halaman upload data Excel operasional:
- Upload file `.xls` / `.xlsx`
- Parsing otomatis bulan/tahun dari isi file (dengan fallback input manual)
- Multi-sheet support (pilih sheet jika lebih dari 1)
- Deteksi duplikat data (opsi overwrite)
- Input runway capacity per periode tanggal
- Pilih cabang tujuan
- Setelah sukses, redirect ke halaman Summary

**Format Excel yang Didukung:**
```
Row: "Time" header row
Kolom 1..31: Data per tanggal
Rows setelah header:
  - 24 baris hourly (00:00 - 23:00)
  - Baris Dom Arr, Dom Dep
  - Baris Int Arr, Int Dep
  - Baris Training Arr, Training Dep
```

### 4. 📋 Summary (`/summary`)
**Controller:** `SummaryController` (310 LOC)
**View:** `summary.blade.php`

Halaman tabel data dan export:
- Tabel data penerbangan per hari (AJAX-loaded)
- Filter bulan, tahun, cabang
- Grafik interaktif: Peak Movement, Traffic Dep/Arr, Peak Hour Tabulation
- **Export Excel** — tabel data + grafik (per-item atau lengkap)
- **Export PDF** — laporan format PDF via DomPDF

---

## 🛤 API Routes

| Method | URI | Controller@Method | Fungsi |
|---|---|---|---|
| `GET` | `/` | `HomeController@index` | Landing page |
| `GET` | `/dashboard` | `DashboardController@index` | Dashboard utama |
| `GET` | `/dashboard/kpi-data` | `DashboardController@getKpiData` | AJAX: KPI comparison data |
| `POST` | `/dashboard/notes` | `DashboardController@saveNote` | AJAX: Simpan catatan harian |
| `DELETE` | `/dashboard/notes` | `DashboardController@deleteNote` | AJAX: Hapus catatan harian |
| `POST` | `/dashboard/events` | `EventController@store` | Tambah event baru |
| `DELETE` | `/dashboard/events/{id}` | `EventController@destroy` | Hapus event |
| `GET` | `/upload` | `UploadController@index` | Halaman upload |
| `POST` | `/upload` | `UploadController@store` | Proses upload Excel |
| `POST` | `/upload/check` | `UploadController@check` | AJAX: Pre-check file Excel |
| `GET` | `/summary` | `SummaryController@index` | Halaman summary |
| `GET` | `/summary/data` | `SummaryController@getData` | AJAX: Data tabel + chart |
| `POST` | `/summary/export` | `SummaryController@exportExcel` | Export ke Excel |
| `POST` | `/summary/export-pdf` | `SummaryController@exportPDF` | Export ke PDF |
| `POST` | `/cabang` | `CabangController@store` | Tambah cabang baru |
| `PUT` | `/cabang/{id}` | `CabangController@update` | Update data cabang |

---

## 🔄 Alur Data (Data Pipeline)

```
  ┌────────────────────────┐
  │   File Excel (.xlsx)   │
  │   Data TWR-AFIS ATC    │
  └───────────┬────────────┘
              │ Upload via /upload
              ▼
  ┌────────────────────────┐
  │    UploadController    │
  │  - parseMetadata()     │
  │  - Extract hourly data │
  │  - Extract categories  │
  │  - Validate format     │
  └───────────┬────────────┘
              │ DB Transaction
              ▼
  ┌────────────────────────────────────────┐
  │           Database (MySQL)             │
  │                                        │
  │  daily_flight_stats ◄── Aggregated     │
  │  raw_flight_datas   ◄── Hourly (h00-23)│
  │  events             ◄── User-created   │
  │  daily_notes        ◄── User-created   │
  │  cabangs            ◄── Master data    │
  └───────────┬────────────────────────────┘
              │ Query (DashboardController + DashboardService)
              ▼
  ┌────────────────────────────────────────┐
  │     Server-Side Computation            │
  │  - Trend data (daily/monthly)          │
  │  - Arr/Dep split                       │
  │  - Category composition                │
  │  - Day-of-week patterns                │
  │  - Hourly profiles per DOW             │
  │  - Peak hour frequency                 │
  │  - Heatmap data + notes                │
  │  - Event stats + hourly averages       │
  │  - KPI calculations + growth %         │
  │  - Yearly comparison data              │
  └───────────┬────────────────────────────┘
              │ window.DashboardData = @json(...)
              ▼
  ┌────────────────────────────────────────┐
  │     Client-Side Rendering (JS)         │
  │  dashboard.js (1800+ LOC)              │
  │  - Chart.js chart instances            │
  │  - Calendar heatmap renderer           │
  │  - Drill-down modals                   │
  │  - KPI card updater                    │
  │  - Tooltip system                      │
  │  - AJAX save/delete notes              │
  │  - Event annotation click handlers     │
  └────────────────────────────────────────┘
```

---

## 📁 Struktur File Proyek

```
Project-Dashboard-Aktualisasi-Airnav/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── EventController.php        # CRUD Event
│   │   │   ├── CabangController.php           # CRUD Cabang
│   │   │   ├── DashboardController.php        # Dashboard utama (701 LOC)
│   │   │   ├── HomeController.php             # Landing page stats
│   │   │   ├── SummaryController.php          # Tabel + Export
│   │   │   └── UploadController.php           # Excel parser + import
│   │   └── Requests/
│   │       └── DashboardFilterRequest.php     # Validasi filter dashboard
│   ├── Models/
│   │   ├── Cabang.php                         # Master cabang bandara
│   │   ├── DailyFlightStat.php                # Statistik harian agregat
│   │   ├── DailyNote.php                      # Catatan per tanggal
│   │   ├── Event.php                          # Event operasional
│   │   └── RawFlightData.php                  # Data hourly mentah
│   └── Services/
│       └── DashboardService.php               # Business logic dashboard
├── database/
│   └── migrations/
│       ├── create_daily_flight_stats_table     # Tabel statistik harian
│       ├── create_events_table                 # Tabel event
│       ├── create_cabangs_table                # Tabel cabang (+ seeder)
│       ├── create_raw_flight_data_table        # Tabel data hourly
│       ├── create_daily_notes_table            # Tabel catatan
│       └── add_description_to_events_table     # Kolom deskripsi event
├── public/
│   └── js/
│       ├── dashboard.js                        # Client-side logic (1800+ LOC)
│       └── libs/
│           ├── chart.umd.min.js               # Chart.js
│           ├── chartjs-plugin-annotation.min.js
│           ├── chartjs-plugin-datalabels.min.js
│           ├── chartjs-plugin-zoom.min.js
│           ├── hammer.min.js                  # Touch gestures (for zoom)
│           └── tailwindcss.js                 # Tailwind CSS (CDN fallback)
├── resources/
│   └── views/
│       ├── home.blade.php                     # Landing page
│       ├── dashboard.blade.php                # Dashboard (85KB)
│       ├── upload.blade.php                   # Upload Excel
│       ├── summary.blade.php                  # Summary + Export
│       ├── components/
│       │   ├── chart-card.blade.php           # Reusable chart wrapper
│       │   └── kpi-card.blade.php             # Reusable KPI card
│       ├── layouts/                           # Layout templates
│       ├── partials/                          # Partial views
│       └── pdf/                               # PDF export templates
├── routes/
│   ├── web.php                                # Semua route definitions
│   └── debug.php                              # Debug routes (dev only)
├── composer.json
├── package.json
└── vite.config.js
```

---

## ⚙ Instalasi & Setup

### Prasyarat Umum

| Software | Versi Minimum | Keterangan |
|---|---|---|
| PHP | 8.2+ | Dengan ekstensi: `mbstring`, `xml`, `zip`, `gd`, `mysql`, `fileinfo` |
| Composer | 2.x | PHP dependency manager |
| Node.js | 18+ | JavaScript runtime |
| npm | 9+ | Node package manager (bawaan Node.js) |
| MySQL | 8.0+ | Atau MariaDB 10.6+ |
| Git | 2.x | Version control |

---

### 🪟 Panduan Instalasi — Windows

#### Langkah 1: Install Laragon (Direkomendasikan)

Laragon adalah all-in-one development environment yang sudah include PHP, MySQL, dan Apache.

1. Download **Laragon Full** dari [laragon.org/download](https://laragon.org/download/)
2. Jalankan installer, pilih lokasi instalasi (default: `C:\laragon`)
3. Setelah install, **buka Laragon** dan klik **"Start All"**
4. Laragon menyediakan:
   - ✅ PHP 8.2+ (sudah include extensions yang dibutuhkan)
   - ✅ MySQL / MariaDB
   - ✅ Apache
   - ✅ Terminal bawaan

> **Alternatif:** Jika tidak menggunakan Laragon, install masing-masing:
> - PHP: [windows.php.net](https://windows.php.net/download/)
> - MySQL: [dev.mysql.com/downloads](https://dev.mysql.com/downloads/mysql/)
> - Composer: [getcomposer.org](https://getcomposer.org/download/)
> - Node.js: [nodejs.org](https://nodejs.org/)

#### Langkah 2: Install Composer (jika belum ada)

```powershell
# Cek apakah Composer sudah terinstall
composer --version

# Jika belum, download installer dari https://getcomposer.org/download/
# Atau jika menggunakan Laragon, Composer sudah tersedia
```

#### Langkah 3: Install Node.js (jika belum ada)

```powershell
# Cek apakah Node.js sudah terinstall
node --version
npm --version

# Jika belum, download dari https://nodejs.org/ (pilih LTS)
```

#### Langkah 4: Clone & Setup Project

Buka **Laragon Terminal** (atau PowerShell/CMD), lalu:

```powershell
# Masuk ke direktori web Laragon
cd C:\laragon\www

# Clone repository
git clone <repository-url> Project-Dashboard-Aktualisasi-Airnav
cd Project-Dashboard-Aktualisasi-Airnav

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Salin file environment
copy .env.example .env

# Generate application key
php artisan key:generate
```

#### Langkah 5: Buat Database

1. Buka **Laragon** → klik kanan icon di system tray → **MySQL** → **HeidiSQL** (atau gunakan phpMyAdmin)
2. Buat database baru:
   ```sql
   CREATE DATABASE airnav_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Atau via terminal:
   ```powershell
   mysql -u root -e "CREATE DATABASE airnav_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

#### Langkah 6: Konfigurasi .env

Buka file `.env` di root project dan sesuaikan:

```env
APP_NAME="AirNav Dashboard"
APP_URL=http://airnav-dashboard.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airnav_dashboard
DB_USERNAME=root
DB_PASSWORD=
```

> **Catatan Laragon:** Secara default, MySQL di Laragon tidak memiliki password (`DB_PASSWORD=` kosong) dan berjalan di port `3306`.

#### Langkah 7: Jalankan Migrasi Database

```powershell
php artisan migrate
```

Ini akan membuat 5 tabel dan meng-seed 8 cabang default (Surabaya, Banyuwangi, Malang, dll.).

#### Langkah 8: Jalankan Aplikasi

**Opsi A — Menggunakan Laragon (Auto Virtual Host):**
1. Laragon secara otomatis membuat virtual host: `http://project-dashboard-aktualisasi-airnav.test`
2. Pastikan Laragon sudah running (klik "Start All")
3. Buka browser ke URL tersebut

**Opsi B — Menggunakan artisan serve:**
```powershell
php artisan serve
# Akses di http://127.0.0.1:8000
```

**Opsi C — Full Development Mode (concurrent):**
```powershell
composer run dev
# Menjalankan: PHP server + Queue + Logs + Vite secara bersamaan
# Akses di http://127.0.0.1:8000
```

---

### 🍎 Panduan Instalasi — macOS

#### Langkah 1: Install Homebrew (jika belum ada)

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

#### Langkah 2: Install PHP 8.2+

```bash
# Install PHP
brew install php

# Verifikasi
php --version

# Pastikan ekstensi yang dibutuhkan aktif (biasanya sudah default di Homebrew PHP)
php -m | grep -E "mbstring|xml|zip|gd|mysql|fileinfo"
```

> Jika ada ekstensi yang belum aktif, edit file `php.ini`:
> ```bash
> php --ini  # cari lokasi php.ini
> # Hapus komentar (;) pada baris extension yang dibutuhkan
> ```

#### Langkah 3: Install MySQL

```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Amankan instalasi (opsional, set root password)
mysql_secure_installation

# Verifikasi
mysql --version
```

#### Langkah 4: Install Composer

```bash
# Install via Homebrew
brew install composer

# Verifikasi
composer --version
```

#### Langkah 5: Install Node.js

```bash
# Install via Homebrew
brew install node

# Verifikasi
node --version
npm --version
```

#### Langkah 6: Clone & Setup Project

```bash
# Clone repository ke direktori pilihan
cd ~/Sites  # atau direktori manapun yang diinginkan
git clone <repository-url> Project-Dashboard-Aktualisasi-Airnav
cd Project-Dashboard-Aktualisasi-Airnav

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Langkah 7: Buat Database

```bash
# Masuk ke MySQL shell
mysql -u root

# Buat database
CREATE DATABASE airnav_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### Langkah 8: Konfigurasi .env

```bash
# Buka file .env dengan editor
nano .env
# atau
code .env  # jika menggunakan VS Code
```

Sesuaikan konfigurasi database:

```env
APP_NAME="AirNav Dashboard"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=airnav_dashboard
DB_USERNAME=root
DB_PASSWORD=       # kosong jika belum di-set, atau isi sesuai mysql_secure_installation
```

#### Langkah 9: Jalankan Migrasi Database

```bash
php artisan migrate
```

#### Langkah 10: Jalankan Aplikasi

**Opsi A — artisan serve (Simple):**
```bash
php artisan serve
# Akses di http://127.0.0.1:8000
```

**Opsi B — Full Development Mode (concurrent):**
```bash
composer run dev
# Menjalankan: PHP server + Queue + Logs + Vite secara bersamaan
```

**Opsi C — Laravel Valet (Opsional, untuk virtual host otomatis):**
```bash
# Install Valet
composer global require laravel/valet
valet install

# Park direktori project
cd ~/Sites
valet park

# Akses di http://project-dashboard-aktualisasi-airnav.test
```

---

### 🚀 Setup Otomatis (Windows & macOS)

Jika semua prasyarat sudah terinstall, gunakan setup script bawaan:

```bash
composer run setup
```

Script ini secara otomatis akan:
1. `composer install` — install PHP dependencies
2. Copy `.env.example` → `.env`
3. `php artisan key:generate` — generate app key
4. `php artisan migrate --force` — migrasi database
5. `npm install` — install Node dependencies
6. `npm run build` — build frontend assets

---

### 🔧 Verifikasi Instalasi

Setelah setup selesai, pastikan semua berjalan dengan baik:

```bash
# 1. Cek status environment
php artisan about

# 2. Cek koneksi database
php artisan db:show

# 3. Cek routes terdaftar
php artisan route:list

# 4. Jalankan server
php artisan serve
```

Buka browser dan akses `http://127.0.0.1:8000`. Jika halaman home muncul, instalasi berhasil! ✅

---

### ❓ Troubleshooting

| Masalah | Solusi |
|---|---|
| **`composer install` gagal** — memory limit | Jalankan: `php -d memory_limit=-1 $(which composer) install` |
| **`php artisan migrate` error** — access denied | Pastikan kredensial MySQL di `.env` sudah benar |
| **Halaman blank / 500 error** | Jalankan: `php artisan config:clear && php artisan cache:clear` |
| **CSRF token mismatch** | Pastikan `APP_KEY` sudah di-generate: `php artisan key:generate` |
| **Excel upload gagal** | Pastikan ekstensi PHP `zip` dan `gd` aktif |
| **Chart tidak muncul** | Buka browser DevTools (F12) → Console, cek error JavaScript |
| **Permission denied (macOS)** | Jalankan: `chmod -R 775 storage bootstrap/cache` |
| **Permission denied (Windows)** | Klik kanan folder `storage` → Properties → Security → Edit → Full Control |
| **Port 8000 sudah dipakai** | Gunakan port lain: `php artisan serve --port=8080` |
| **MySQL service tidak jalan (macOS)** | Jalankan: `brew services restart mysql` |
| **MySQL service tidak jalan (Windows)** | Buka Laragon → klik "Start All", atau jalankan via services.msc |

---

## 📖 Penggunaan

### 1. Upload Data
1. Buka halaman **Upload** (`/upload`)
2. Pilih **cabang** tujuan
3. Upload file **Excel** (.xls/.xlsx) sesuai format TWR-AFIS
4. Sistem akan otomatis mendeteksi bulan/tahun dari file
5. Masukkan **runway capacity** per periode (jika berbeda antar tanggal)
6. Klik **Proses** — data akan diimport dan di-redirect ke Summary

### 2. Analisis Dashboard
1. Buka halaman **Dashboard** (`/dashboard`)
2. Gunakan **filter** (tahun, bulan, cabang) untuk fokus pada periode tertentu
3. Interaksi:
   - **Klik bar** di pola mingguan → drill-down profil jam
   - **Klik tanggal** di heatmap → drill-down detail hari
   - **Klik event area** di grafik tren → detail event
   - **Tombol "Catatan"** di heatmap → lihat/hapus semua catatan
   - **Zoom/pan** di grafik tren dan seasonality

### 3. Export Laporan
1. Buka halaman **Summary** (`/summary`)
2. Pilih periode dan cabang
3. Export sebagai **Excel** (tabel, grafik, atau lengkap) atau **PDF**

---

## 📦 Dependensi

### PHP (Composer)

| Package | Versi | Fungsi |
|---|---|---|
| `laravel/framework` | ^12.0 | Framework utama |
| `phpoffice/phpspreadsheet` | ^1.30 | Parsing & export file Excel |
| `barryvdh/laravel-dompdf` | ^3.1 | Export laporan PDF |
| `maatwebsite/excel` | ^3.1 | Helper Excel (legacy) |
| `laravel/tinker` | ^2.10.1 | Debug console |

### JavaScript (Frontend — loaded locally)

| Library | Fungsi |
|---|---|
| `Chart.js` (chart.umd.min.js) | Rendering semua grafik |
| `chartjs-plugin-annotation` | Overlay event di grafik tren |
| `chartjs-plugin-datalabels` | Label persentase di donut chart |
| `chartjs-plugin-zoom` | Zoom + pan pada grafik |
| `Hammer.js` | Touch gesture (pinch-to-zoom) |
| `Tailwind CSS` | Styling (CDN) |

---

## 📝 Catatan Teknis

- **Timezone:** Semua data hourly disimpan dalam **UTC 0**. Konversi ke WIB (UTC+7) dilakukan di sisi presentasi.
- **Heatmap rendering** dilakukan sepenuhnya di **client-side** — data heatmap dikirim via `window.DashboardData.heatmapData` dan dirender sebagai HTML grid oleh JavaScript.
- **Chart.js instances** dikelola secara ketat — setiap instance di-destroy sebelum dibuat ulang untuk mencegah memory leak.
- **CSRF protection** aktif pada semua AJAX POST/DELETE requests.
- **Duplikat data** dicegah dengan constraint `UNIQUE(date, branch_code)` dan logic `updateOrCreate` pada upload.
- **Lazy data:** Semua data dashboard dihitung di server dan dikirim sekaligus ke frontend via `@json()` di blade template. Tidak ada lazy loading per-chart.

---

## 📄 Lisensi

© 2026 AirNav Indonesia Cabang Surabaya. **CONFIDENTIAL DATA — FOR INTERNAL USE ONLY.**
