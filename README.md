<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="160" alt="Laravel Logo">
</p>

# Daily Sync

Daily Sync adalah dashboard produktivitas all-in-one yang menggabungkan Pomodoro timer, pelacak kebiasaan harian, dan catatan tempel dalam satu aplikasi web berbasis Laravel.

## Fitur

- **Pomodoro Timer**

  Sesi fokus dan istirahat mengikuti metode Pomodoro klasik (4 ronde), durasi kustom per mode, notifikasi audio, dan pencatatan sesi fokus yang selesai ke database.

- **Habit Checklist**

  Kelola kebiasaan harian (tambah, ubah nama, hapus), cap kebiasaan per hari, pantau progress, dan lihat streak hari beruntun.

- **Export Riwayat Habit**

  Laporan bulanan per habit yang bisa dicetak atau disimpan sebagai PDF lewat dialog cetak browser, dengan navigasi antar bulan.

- **Daily Quick Notes**

  Sticky notes board dengan preset warna (kuning, sky, coral), pin dekoratif, dan dukungan edit isi serta warna.

- **Dashboard Ringkasan**

  Halaman utama yang menampilkan capaian hari ini: kebiasaan yang sudah dicap, sesi fokus, catatan, streak, dan preview kebiasaan yang belum selesai.

- **Dark / Light Mode**

  Toggle tema terang dan gelap yang tersimpan di browser.

## Teknologi

- Laravel 13 (PHP 8.3)
- MySQL sebagai database utama
- Tailwind CSS v4 + Vite untuk aset frontend
- Vanilla JavaScript untuk interaksi halaman
- Pest + PHPUnit untuk pengujian
- GitHub Actions untuk CI

## Struktur Halaman

| URL | Keterangan |
| --- | --- |
| `/` | Dashboard ringkasan harian |
| `/habits` | Habit Checklist |
| `/habits/export` | Laporan riwayat habit bulanan (print/PDF) |
| `/pomodoro` | Pomodoro Timer |
| `/notes` | Daily Quick Notes |

## Instalasi Lokal

1. Salin konfigurasi environment dan sesuaikan kredensial database:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

2. Atur koneksi database di `.env` (default contoh memakai sqlite; untuk MySQL ubah `DB_CONNECTION=mysql` beserta host, port, database, dan username).

3. Install dependency:

   ```bash
   composer install
   npm install
   ```

4. Jalankan migrasi:

   ```bash
   php artisan migrate
   ```

5. Jalankan aplikasi:

   ```bash
   npm run dev
   php artisan serve
   ```

   Lalu buka `http://localhost:8000`.

Untuk aset produksi, jalankan `npm run build` sebagai pengganti `npm run dev`.

## Testing

Suite test memakai sqlite in-memory secara otomatis, jadi tidak memerlukan MySQL yang aktif:

```bash
php artisan test --compact
```

## CI/CD

Workflow `.github/workflows/ci.yml` berjalan saat ada pull request atau push ke branch `main`, dengan dua job:

- **Backend tests**

  Instalasi Composer, persiapan environment, dan `php artisan test`.

- **Frontend build**

  Instalasi npm dan `npm run build`.
