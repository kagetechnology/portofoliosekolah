# Changelog

Semua perubahan penting aplikasi dicatat di file ini.

## [1.2] - 2026-08-23

### Ditambahkan

- Admin dapat mereset password siswa ke password default.
- Admin dapat mengatur password default siswa melalui menu Profil Sekolah.
- Password default dipakai untuk reset password dan akun siswa hasil import.
- Password default sekolah disimpan terenkripsi di database.
- Siswa wajib mengganti password setelah password direset atau akun diimport.
- Notifikasi bagikan preview muncul setelah portofolio dibuat.
- Signed preview URL untuk portofolio yang belum disetujui.
- Preview portofolio hanya dapat dibuka pengguna aktif yang sudah login.
- Project tim memiliki undangan kontribusi dengan status menunggu, diterima, atau ditolak.
- Anggota tim dapat mengonfirmasi apakah project merupakan karya mereka.
- Project tim yang diterima tampil pada dashboard, daftar karya, profil publik, dan CV anggota.
- Status anggota tim ditampilkan pada halaman moderasi admin.
- Pencarian anggota tim berdasarkan nama atau kelas.
- Pemilih anggota tim dengan checkbox, indikator jumlah, chip pilihan, dan batas 20 anggota.
- Halaman CV siswa publik yang dibuat otomatis dari profil, skill, karya, dan sertifikat terverifikasi.
- Tombol Print / Simpan PDF pada CV siswa.
- Menu CV Saya dan Print CV pada dashboard siswa.
- Menu Bagikan dan Bagikan Preview pada dashboard serta daftar portofolio siswa.
- Skrip `deploy-hosting.sh` untuk deployment ke `/www/wwwroot/taksu.smkn1mas.sch.id`.
- Dokumentasi konteks produk pada `PRODUCT.md`.
- Catatan alasan penolakan portofolio yang wajib diisi admin atau guru.
- Form edit portofolio dan penilaian skill untuk admin serta guru.
- Import akun guru dari file XLSX daftar guru Dapodik menggunakan password default sekolah.
- Panduan (guidance) multimedia interaktif untuk cover (rasio 16:9), unggah screenshot, dan sematan video demo.
- Tombol generator kerangka struktur deskripsi otomatis (Latar Belakang, Fitur, Peran, Hasil).
- Penghitung karakter real-time dengan status indikator minimal 300 karakter teks.
- Pipeline kompresi gambar otomatis (`ImageOptimizer`): mengonversi cover karya, foto editor, avatar, dan logo menjadi format modern WebP dengan resize proporsional, preservasi transparansi, dan pembersihan metadata EXIF.
- Perintah Artisan `php artisan images:convert-to-webp` untuk mengonversi seluruh gambar lama yang sudah tersimpan di storage dan database ke WebP.

### Diubah

- Versi aplikasi dinaikkan dari `1.0` menjadi `1.2`.
- Batas unggah file gambar ditingkatkan hingga 5MB-10MB karena seluruh file otomatis dikompresi menjadi 50-150KB saat disimpan.
- Deskripsi portofolio diubah menjadi wajib diisi dengan batas minimal 300 karakter teks asli (tidak menghitung tag HTML kosong).
- Footer membaca versi dari `APP_VERSION` atau fallback konfigurasi aplikasi.
- Daftar karya siswa kini mencakup project milik sendiri dan kontribusi tim yang sudah diterima.
- Kontributor dapat melihat dan membagikan project approved, tetapi tidak dapat mengedit atau menghapus project milik siswa lain.
- Anggota project tim lama otomatis dianggap diterima agar karya lama tidak hilang.
- Halaman project publik hanya menampilkan anggota tim yang sudah menerima undangan.
- Form pemilihan anggota tim tidak lagi membutuhkan Ctrl/Cmd.
- Form portofolio diperbaiki agar tidak mengalami horizontal overflow pada mobile.
- Konfigurasi storage mendukung path hosting absolut dengan fallback otomatis untuk lokal.
- Halaman CV tidak lagi bergantung pada Vite manifest sehingga tetap dapat dicetak ketika `public/build` belum tersedia di hosting.
- Skrip deployment membangun asset frontend otomatis jika npm tersedia.
- Tailwind CSS disajikan sepenuhnya dari build lokal; Tailwind CDN dihapus untuk mengurangi waktu download dan kompilasi CSS di browser.
- CKEditor dimuat hanya pada halaman yang memiliki rich text editor.
- Pencarian anggota tim dipindahkan ke endpoint AJAX dengan debounce dan maksimal 20 hasil, sehingga form tidak lagi merender lebih dari 1.200 siswa.
- Ringkasan skill direktori siswa dihitung secara batch untuk menghilangkan query N+1.
- Gambar kartu memakai native lazy loading dan asset statis mendapatkan cache header browser.
- Siswa dapat memperbaiki portofolio yang ditolak dan mengirimnya kembali ke antrian review.
- Pengisian skill dipindahkan dari siswa ke admin atau guru.

### Keamanan

- Endpoint reset password dibatasi untuk akun siswa dan admin yang terotorisasi.
- Password disimpan menggunakan hash Laravel.
- Password default sekolah disimpan memakai encrypted cast Laravel.
- Preview portofolio dilindungi autentikasi, status akun aktif, kewajiban mengganti password awal, dan validasi signature URL.
- Preview tidak menambah statistik tayangan publik.
- CV tidak menampilkan NISN atau data sensitif siswa.
- Signed preview URL tidak dapat dimanipulasi untuk membuka project lain.

### Database

- Menambahkan kolom `student_default_password` pada tabel `schools`.
- Menambahkan kolom `status` dan `responded_at` pada tabel `portfolio_contributors`.
- Migrasi bersifat non-destruktif dan tidak menghapus database, gambar, atau file upload.

### Deployment

Jalankan perintah berikut setelah source versi 1.2 diunggah ke hosting:

```bash
cd /www/wwwroot/taksu.smkn1mas.sch.id
php artisan down
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan storage:link --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan up
```

Alternatif satu perintah:

```bash
bash /www/wwwroot/taksu.smkn1mas.sch.id/deploy-hosting.sh
```

Jangan menimpa `.env`, mengganti `APP_KEY`, menjalankan `migrate:fresh`, atau menghapus `storage/app/public` saat deployment.

### Verifikasi

- Migrasi versi 1.2 berhasil dijalankan pada database MySQL lokal.
- CV publik berhasil diakses melalui desktop dan mobile.
- CV mobile tidak mengalami horizontal overflow.
- Project tim nyata berhasil tampil pada profil dan CV anggota yang diterima.
- Pemilih anggota tim diuji dengan lebih dari 1.200 akun siswa aktif.
- Blade template berhasil dikompilasi.
- Build frontend berhasil.
- Laravel Pint berhasil.
- Pemeriksaan whitespace Git berhasil.
- Test PHPUnit tersedia, tetapi eksekusi lokal terblokir karena ekstensi PHP `pdo_sqlite` belum terpasang.

### Catatan URL Siswa

Slug siswa dibuat unik. Jika beberapa siswa memiliki nama sama, URL menggunakan suffix angka:

```text
/siswa/budi-santoso
/siswa/budi-santoso-2
/siswa/budi-santoso-3
```
