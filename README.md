# Amar Cell Service

Sistem manajemen servis HP lengkap berbasis PHP dan MySQL untuk membantu teknisi HP rumahan dalam mengelola bisnisnya secara profesional.

## 🚀 Fitur Utama

### Untuk Pelanggan
- **Beranda Interaktif**: Tampilan modern dengan informasi lengkap layanan
- **Analisis Kerusakan Otomatis**: Estimasi biaya perbaikan berdasarkan jenis HP dan gejala kerusakan
- **Booking Servis Online**: Pemesanan servis dengan jadwal yang dapat dipilih
- **Katalog Produk**: Lengkap sparepart dan HP bekas dengan harga transparan
- **Testimoni Pelanggan**: Sistem ulasan dan rating untuk layanan
- **Profil Usaha**: Informasi lengkap tentang Amar Cell Service

### Untuk Admin (Amar)
- **Dashboard Analitis**: Ringkasan data servis, stok, dan pendapatan
- **Manajemen Servis**: CRUD lengkap dengan tracking status pengerjaan
- **Manajemen Barang**: Stok sparepart dan HP bekas dengan alert stok menipis
- **Manajemen Pelanggan**: Database pelanggan dengan histori servis
- **Transaksi Penjualan**: Sistem kasir dengan otomatisasi pengurangan stok
- **Laporan Keuangan**: Laporan harian, bulanan, dan tahunan
- **Testimoni Management**: Moderasi ulasan pelanggan
- **Pengaturan Sistem**: Konfigurasi toko dan backup data

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8+ dengan PDO untuk keamanan database
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3
- **Icons**: Bootstrap Icons
- **Charts**: Chart.js untuk visualisasi data
- **Responsive Design**: Mobile-first approach

## 📁 Struktur Direktori

```
/
├── .htaccess
├── README.md
├── admin/
│   ├── barang.php
│   ├── barang_tambah.php
│   ├── dashboard.php
│   ├── index.php
│   ├── laporan.php
│   ├── laporan_export.php
│   ├── login.php
│   ├── logout.php
│   ├── pelanggan.php
│   ├── pengaturan.php
│   ├── servis.php
│   ├── servis_tambah.php
│   ├── template.php
│   ├── testimoni_admin.php
│   ├── transaksi.php
│   ├── transaksi_tambah.php
│   └── views/
│       └── sidebar_admin.php
├── amarcell_database.sql
├── analisis.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── booking.php
├── config/
│   ├── config.php
│   └── database.php
├── controllers/
│   └── ServisControllers.php
├── index.php
├── katalog.php
├── kontak.php
├── models/
│   ├── Admin.php
│   ├── Barang.php
│   ├── GeminiAI.php
│   ├── Pelanggan.php
│   ├── Pengaturan.php
│   ├── Servis.php
│   ├── Testimoni.php
│   └── Transaksi.php
├── testimoni.php
└── views/
    ├── footer.php
    └── header.php
```

## 🚀 Instalasi

### Persyaratan Sistem
- PHP 7.0 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Composer (opsional)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   git clone https://github.com/username/amarcell-service.git
   cd amarcell-service
   ```

2. **Import Database**
   - Buat database baru di MySQL: `CREATE DATABASE amarcell_db;`
   - Import file SQL: `mysql -u root -p amarcell_db < amarcell_database.sql`

3. **Konfigurasi Database**
   - Buka file `config/database.php`
   - Sesuaikan pengaturan koneksi:
   ```php
   private $host = "localhost";
   private $db_name = "amarcell_db";
   private $username = "root";     // Ganti dengan username MySQL Anda
   private $password = "";         // Ganti dengan password MySQL Anda
   ```

4. **Konfigurasi Umum**
   - Buka file `config/config.php`
   - Sesuaikan pengaturan sesuai kebutuhan:
   ```php
   $base_url = "http://localhost/amarcell/";
   $whatsapp_number = "6281234567890"; // Nomor WhatsApp bisnis
   ```

5. **Setup Folder Uploads**
   - Pastikan folder `uploads/` memiliki permission write (777)
   ```bash
   chmod -R 777 uploads/
   ```

6. **Akses Aplikasi**
   - Frontend: `http://localhost/amarcell/`
   - Admin Panel: `http://localhost/amarcell/admin/`
   - Login Admin:
     - Username: `admin`
     - Password: `password` (ubah segera setelah login)

## 🔧 Konfigurasi Lanjutan

### Pengaturan WhatsApp Notifikasi
Untuk mengaktifkan notifikasi WhatsApp, konfigurasi di `config/config.php`:
```php
$whatsapp_api_url = "https://api.whatsapp.com/send";
$whatsapp_number = "6281234567890"; // Format internasional
```

### Pengaturan Email (Opsional)
Untuk fitur email notifikasi, konfigurasi SMTP di `config/config.php`:
```php
$email_host = "smtp.gmail.com";
$email_port = 587;
$email_user = "your-email@gmail.com";
$email_pass = "your-app-password";
```

## 📱 Penggunaan

### Untuk Pelanggan
1. **Analisis Kerusakan**: Pilih jenis HP dan gejala untuk estimasi biaya
2. **Booking Servis**: Isi form dengan data diri dan jadwal servis
3. **Lihat Katalog**: Jelajahi sparepart dan HP bekas
4. **Beri Testimoni**: Bagikan pengalaman setelah servis

### Untuk Admin
1. **Dashboard**: Monitor aktivitas bisnis secara real-time
2. **Kelola Servis**: Update status pengerjaan dan kelola data servis
3. **Kelola Barang**: Tambah, edit, dan monitor stok produk
4. **Kelola Transaksi**: Catat penjualan dengan otomatisasi stok
5. **Laporan**: Analisis kinerja bisnis dengan chart dan statistik

## 🔒 Keamanan

- **SQL Injection Protection**: Menggunakan PDO prepared statements
- **XSS Protection**: Semua input di-sanitize dengan `htmlspecialchars()`
- **CSRF Protection**: Token validation untuk form penting
- **Session Management**: Session hijacking protection
- **File Upload Security**: Validasi tipe dan ukuran file

## 🎨 Kustomisasi

### Mengubah Tema Warna
Edit file `assets/css/style.css` dan ubah CSS variables:
```css
:root {
    --primary-color: #2563eb;    /* Warna utama */
    --secondary-color: #64748b;  /* Warna sekunder */
    --accent-color: #06b6d4;     /* Warna aksen */
}
```

### Menambah Fitur Analisis Kerusakan
Edit file `analisis.php` dan tambah database kerusakan di array `$kerusakan_db`.

### Custom Logo dan Branding
1. Upload logo ke folder `uploads/`
2. Update di halaman `admin/pengaturan.php`
3. Logo akan muncul di seluruh website

## 📊 Database Schema

### Tabel Utama
- `admin`: Data admin sistem
- `pelanggan`: Data pelanggan
- `servis`: Data servis HP
- `barang`: Data produk (sparepart & HP bekas)
- `transaksi`: Data transaksi penjualan
- `testimoni`: Ulasan pelanggan
- `pengaturan`: Konfigurasi sistem

## 🐛 Troubleshooting

### Masalah Umum

1. **Koneksi Database Gagal**
   - Pastikan MySQL berjalan
   - Cek konfigurasi di `config/database.php`
   - Verifikasi username dan password

2. **Gambar Tidak Muncul**
   - Cek permission folder `uploads/` (harus 777)
   - Pastikan GD library terinstall di PHP

3. **Session Tidak Berfungsi**
   - Cek `session.save_path` di php.ini
   - Pastikan tidak ada spasi di file PHP sebelum `<?php`

4. **Error 404 di Sub-directory**
   - Pastikan `.htaccess` ada di root directory
   - Cek konfigurasi web server

### Log Error
File error log tersedia di:
- PHP Error Log: `/var/log/php_errors.log` (Linux) atau `php_error.log` (Windows)
- MySQL Error Log: `/var/log/mysql/error.log`

## 🤝 Kontribusi

1. Fork project ini
2. Buat branch fitur Anda (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Menambah fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## 📄 Lisensi

Project ini dilisensikan di bawah MIT License. Lihat file `LICENSE` untuk detail.

## 👨‍💻 Developer

**Amar Cell Service**
- Teknisi HP Profesional
- WhatsApp: [+62 812-3456-7890](https://wa.me/6281234567890)
- Email: amar@amarcell.com
- Lokasi: Jakarta, Indonesia

## 🙏 Acknowledgments

- Terima kasih kepada Bootstrap Team untuk framework CSS
- Chart.js untuk visualisasi data yang menakjubkan
- Bootstrap Icons untuk koleksi icon yang lengkap
- Komunitas PHP Indonesia untuk support dan inspirasi

---

**Versi**: 1.0.0  
**Terakhir Update**: November 2024  
**Dibuat dengan ❤️ untuk UMKM Indonesia**