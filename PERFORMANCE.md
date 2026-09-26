# Optimasi Performa

Dokumen ini mencatat optimasi performa aplikasi Portofolio Sekolah versi 1.2.

## Ringkasan Hasil

Baseline sebelum optimasi:

```text
Homepage TTFB: sekitar 656 ms
FCP/LCP: sekitar 868 ms
Tailwind: CDN dan dikompilasi di browser
CKEditor: dimuat pada semua halaman
Form project tim: merender lebih dari 1.200 siswa
```

Hasil setelah optimasi pada environment lokal:

```text
Homepage TTFB cache miss: sekitar 170 ms
Homepage TTFB cache hit: sekitar 101 ms
FCP/LCP: sekitar 396 ms
CLS: 0
CSS lokal: 68,23 kB / 11,74 kB gzip
Script eksternal homepage: 0
Gambar rusak pada homepage: 0
Horizontal overflow mobile: 0
```

Angka hosting dapat berbeda karena CPU, disk, konfigurasi PHP-FPM, database, jaringan, dan cache server.

## 1. Tailwind CSS Lokal

Tailwind CDN dihapus sepenuhnya.

Sebelumnya browser mengunduh script Tailwind CDN dan mengompilasi utility CSS saat halaman dibuka. Proses ini menambah request eksternal, penggunaan CPU browser, dan waktu render.

Sekarang CSS dibuat saat deployment:

```bash
npm ci --ignore-scripts
npm run build
```

Blade memuat hasil build melalui Vite:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

File wajib tersedia di hosting:

```text
public/build/manifest.json
public/build/assets/app-*.css
```

Tidak ada lagi request ke:

```text
cdn.tailwindcss.com
```

## 2. Font Lokal

Font Archivo dan Space Grotesk tidak lagi diambil dari Google Fonts.

Font dibangun bersama Vite dan disimpan sebagai WOFF2 di:

```text
public/build/assets
```

Tidak ada lagi request ke:

```text
fonts.googleapis.com
fonts.gstatic.com
```

Keuntungan:

- Tidak bergantung koneksi ke layanan pihak ketiga.
- Tidak ada DNS lookup dan TLS handshake tambahan.
- Font dapat memakai cache browser jangka panjang.
- Tampilan lebih konsisten ketika jaringan lambat.

## 3. Lazy-load CKEditor

Sebelumnya CKEditor dimuat global pada semua halaman.

Sekarang CKEditor hanya dimuat jika halaman memiliki:

```html
<textarea data-ckeditor>
```

Contoh halaman yang memuat CKEditor:

- Buat/edit portofolio.
- Edit profil siswa.
- Edit profil sekolah.
- Edit portofolio admin/guru.

Homepage, direktori siswa, dashboard, daftar portofolio, dan CV tidak mengunduh CKEditor.

## 4. Pencarian Anggota Tim AJAX

Sebelumnya form project tim memuat lebih dari 1.200 siswa ke HTML awal.

Dampak sebelumnya:

- HTML besar.
- DOM sangat besar.
- Browser membutuhkan waktu lebih lama untuk parsing.
- JavaScript memproses seluruh daftar siswa.
- Mobile terasa berat.

Sekarang:

- HTML awal tidak memuat daftar siswa.
- Pencarian berjalan setelah minimal 2 karakter.
- Debounce 250 ms.
- Maksimal 20 hasil per request.
- Request sebelumnya dibatalkan jika pengguna terus mengetik.
- Anggota yang sudah dipilih tetap dimuat saat edit.
- Pilihan dipertahankan jika validasi form gagal.

Endpoint:

```text
GET /siswa/contributors/search?q=nama
```

Pengukuran DOM form:

```text
Sebelum pencarian: sekitar 247 node
Setelah satu hasil: sekitar 255 node
```

## 5. Cache Homepage

Data agregasi homepage dicache selama 5 menit:

- Total siswa aktif.
- Total portofolio approved.
- Total skill.
- ID portofolio terbaru.
- ID portofolio populer.
- ID portofolio unggulan.
- Kategori populer.
- ID siswa paling aktif.
- ID skill populer.

Cache hanya menyimpan array, ID, dan nilai primitif. Object Eloquent tidak disimpan agar aman pada driver database, file, dan Redis.

Data detail model tetap dimuat dalam query batch setiap request.

Konsekuensi:

- Data homepage dapat terlambat maksimal 5 menit.
- Halaman daftar dan detail tetap real-time.

Cache key:

```text
public.home.v2
```

Untuk membersihkan cache homepage:

```bash
php artisan cache:forget public.home.v2
```

## 6. Menghapus Query dari Blade

Homepage sebelumnya menjalankan query langsung dari Blade untuk:

- Nama sekolah.
- Jumlah portofolio.

Query dipindahkan ke controller. Data sekolah juga menggunakan `once()` agar hanya diambil satu kali per request walaupun dipakai navbar, halaman, dan footer.

## 7. Menghapus Query N+1 Direktori Siswa

Sebelumnya setiap kartu siswa menjalankan query ringkasan skill sendiri.

Dengan 12 siswa per halaman, query skill dapat dijalankan berulang kali.

Sekarang:

- Project milik siswa diambil secara batch.
- Project kontribusi accepted diambil secara batch.
- Skill semua project diambil dalam satu query batch.
- Ringkasan skill dikelompokkan di memory untuk 12 siswa pada halaman tersebut.

Jumlah query tidak bertambah mengikuti jumlah kartu siswa.

## 8. Lazy Loading Gambar

Gambar pada kartu daftar memakai:

```html
loading="lazy"
decoding="async"
```

Diterapkan pada:

- Kartu portofolio publik.
- Direktori siswa.
- Daftar portofolio siswa.
- Daftar moderasi admin/guru.

Gambar di bawah viewport tidak langsung diunduh.

## 9. Kompresi &amp; Resize Otomatis Unggahan Gambar

Sebelumnya pengguna yang mengunggah foto langsung dari kamera smartphone (3MB–10MB, resolusi 4000x3000 piksel) akan menyebabkan penyimpanan server membengkak dan halaman pengunjung lambat saat mengunduh gambar besar.

Kini ditambahkan pipeline optimasi otomatis (`App\Support\ImageOptimizer`) berbasis PHP GD:

1. **Konversi Otomatis ke WebP**:
   - Seluruh unggahan gambar (JPG, PNG, WebP) otomatis dikonversi ke format WebP modern dengan kompresi cerdas.
   - Ukuran file berkurang **70% hingga 90%** tanpa penurunan visual yang terlihat mata manusia.
2. **Resize Proporsional Sesuai Kebutuhan**:
   - **Cover Portofolio**: Maksimal lebar 1600px &times; tinggi 1000px, kualitas 82 (ukuran akhir ~80–180KB).
   - **Gambar Editor (Dokumentasi/Screenshot)**: Maksimal 1200px &times; 1200px, kualitas 80 (ukuran akhir ~60–120KB).
   - **Foto Profil Siswa/Guru (Avatar)**: Maksimal 400px &times; 400px, kualitas 85 (ukuran akhir ~20–40KB).
   - **Logo Sekolah**: Maksimal 600px &times; 600px, kualitas 85.
   - **Sertifikat Gambar**: Maksimal 1800px &times; 1800px, kualitas 85.
3. **Preservasi Transparansi**:
   - Logo atau grafis berlatar belakang transparan (PNG transparan) tetap mempertahankan transparansi saluran alfa saat diubah ke WebP.
4. **Pembersihan Metadata & Privasi**:
   - Metadata sensitif seperti tag GPS lokasi kamera siswa dihapus saat re-encoding, melindungi privasi siswa sekaligus menghemat ukuran file.
5. **Dukungan File Non-Gambar**:
   - Dokumen PDF (seperti sertifikat format PDF) otomatis dideteksi dan disimpan apa adanya tanpa distorsi.
6. **Perintah Konversi Gambar Lama (Migrasi ke WebP)**:
   - Untuk berkas gambar lama yang terlanjur diunggah sebelum fitur kompresi aktif, jalankan perintah Artisan berikut:
     ```bash
     php artisan images:convert-to-webp
     ```
   - Perintah ini secara otomatis:
     - Mengonversi semua file `.jpg`, `.jpeg`, `.png` di storage menjadi `.webp`.
     - Memperbarui kolom di database (`portfolios.cover_image`, `users.avatar`, `certificates.file`, `schools.logo`).
     - Memperbarui tautan gambar di deskripsi portofolio (`portfolios.description`).
     - Menghapus file lama untuk menghemat kapasitas disk server.
     - Tambahkan `--keep-originals` jika ingin mempertahankan berkas lama sebagai cadangan:
       ```bash
       php artisan images:convert-to-webp --keep-originals
       ```

## 10. Fallback File Upload Hilang

Database dapat memiliki path gambar yang filenya sudah tidak tersedia di storage.

Sebelumnya browser meminta file tersebut, lalu Laravel memberikan response error 403/HTML untuk setiap gambar.

Sekarang model memeriksa file terlebih dahulu:

- Cover portofolio hilang menggunakan placeholder SVG.
- Avatar hilang menggunakan Gravatar.
- Logo sekolah hilang menggunakan ikon sekolah.
- File sertifikat hilang tidak menghasilkan URL rusak.

Ini mengurangi request gagal dan response error yang tidak perlu.

## 11. Cache Browser dan Compression

`public/.htaccess` menambahkan cache browser:

```text
CSS, JS, WOFF, WOFF2: 1 tahun + immutable
AVIF, GIF, ICO, JPG, PNG, SVG, WebP: 1 minggu
```

Compression diaktifkan untuk:

- CSS.
- JavaScript.
- JSON.
- SVG.

Konfigurasi ini berlaku jika Apache mengaktifkan `mod_headers` dan `mod_deflate`.

Jika hosting menggunakan Nginx, atur cache header dan gzip melalui konfigurasi Nginx/aaPanel.

## 12. Cache Production Laravel

Setelah update hosting, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Fungsi cache:

- `config:cache`: menggabungkan konfigurasi Laravel.
- `route:cache`: menggabungkan definisi route.
- `view:cache`: mengompilasi Blade sebelum request pengguna.

Jangan mengubah `.env` setelah `config:cache` tanpa menjalankan ulang:

```bash
php artisan config:clear
php artisan config:cache
```

## 13. Build Manual Hosting

Setelah semua source di-upload:

```bash
cd /www/wwwroot/taksu.smkn1mas.sch.id

php artisan down

composer install --no-dev --optimize-autoloader --no-interaction

npm ci --ignore-scripts
npm run build

test -f public/build/manifest.json && echo "Vite manifest tersedia"

php artisan migrate --force
php artisan storage:link --force

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

php artisan up
```

Jika hosting tidak memiliki npm, build di komputer lokal:

```bash
npm ci
npm run build
```

Kemudian upload seluruh folder:

```text
public/build
```

## 14. Permission Hosting

Hanya folder berikut yang perlu writable:

```text
storage
bootstrap/cache
```

Jika memiliki akses root dan PHP-FPM berjalan sebagai `www`:

```bash
chown -R www:www storage bootstrap/cache
chmod -R u=rwX,g=rwX,o=rX storage bootstrap/cache
```

Jangan menjalankan `chmod -R` pada seluruh project.

Jika muncul `Operation not permitted`, user terminal bukan pemilik file. Jalankan sebagai root atau ubah owner melalui File Manager aaPanel.

## 15. Checklist Verifikasi

Setelah deployment:

```bash
test -f public/build/manifest.json && echo "Build tersedia"
readlink -f public/storage
php artisan migrate:status
```

Periksa melalui browser:

1. Homepage tidak menampilkan error Vite manifest.
2. CSS dan font tampil normal.
3. Tidak ada request ke Tailwind CDN.
4. Homepage tidak memuat CKEditor.
5. Form portofolio dapat memuat CKEditor.
6. Pencarian anggota tim menghasilkan maksimal 20 siswa.
7. Gambar lama atau file hilang memakai fallback.
8. Direktori siswa tetap menampilkan skill kontribusi.
9. Tampilan mobile tidak mengalami horizontal overflow.
10. Print CV tetap berfungsi.
11. Gambar yang diunggah otomatis terkompresi dalam format WebP.

## 16. Catatan Pengembangan

- Jangan memasukkan kembali Tailwind CDN.
- Jangan memuat CKEditor secara global.
- Jangan mengambil seluruh siswa untuk dropdown anggota tim.
- Jangan menjalankan query database dari Blade.
- Hindari query di dalam loop kartu/list.
- Gunakan eager loading untuk relasi yang ditampilkan.
- Gunakan pagination untuk daftar data besar.
- Selalu jalankan `npm run build` setelah menambah class Tailwind baru.
