-- Database: amarcell_db
-- Amar Cell Service Database Structure

CREATE DATABASE IF NOT EXISTS amarcell_db;
USE amarcell_db;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    kontak VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Servis
CREATE TABLE IF NOT EXISTS servis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pelanggan_id INT,
    jenis_hp VARCHAR(50) NOT NULL,
    kerusakan TEXT NOT NULL,
    estimasi_biaya DECIMAL(10,2) DEFAULT 0,
    status ENUM('menunggu', 'dikerjakan', 'selesai', 'diambil') DEFAULT 'menunggu',
    tanggal_masuk TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai TIMESTAMP NULL,
    keterangan TEXT,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE SET NULL
);

-- Tabel Barang
CREATE TABLE IF NOT EXISTS barang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_barang VARCHAR(100) NOT NULL,
    kategori ENUM('sparepart', 'hp_bekas') NOT NULL,
    stok INT DEFAULT 0,
    harga_modal DECIMAL(10,2) DEFAULT 0,
    harga_jual DECIMAL(10,2) DEFAULT 0,
    deskripsi TEXT,
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pelanggan_id INT,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metode_pembayaran ENUM('tunai', 'transfer', 'kredit') DEFAULT 'tunai',
    status ENUM('pending', 'selesai', 'batal') DEFAULT 'pending',
    keterangan TEXT,
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE SET NULL
);

-- Tabel Detail Transaksi
CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_transaksi INT,
    id_barang INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    jasa_pasang BOOLEAN DEFAULT FALSE,
    biaya_pasang DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE
);

-- Tabel Testimoni
CREATE TABLE IF NOT EXISTS testimoni (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pelanggan_id INT,
    isi TEXT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE CASCADE
);

-- Tabel Backup Log
CREATE TABLE IF NOT EXISTS backup_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tanggal_backup TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sukses', 'gagal') DEFAULT 'sukses',
    keterangan TEXT
);

-- Tabel Pengaturan Sistem
CREATE TABLE IF NOT EXISTS pengaturan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_toko VARCHAR(100) DEFAULT 'Amar Cell Service',
    alamat TEXT,
    nomor_whatsapp VARCHAR(20),
    email_bisnis VARCHAR(100),
    jam_buka VARCHAR(50),
    logo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert data default
INSERT INTO admin (username, password, email, nama_lengkap) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@amarcell.com', 'Amar');

INSERT INTO pengaturan (nama_toko, alamat, nomor_whatsapp, email_bisnis, jam_buka) VALUES 
('Amar Cell Service', 'Jl. Contoh No. 123, Kota', '081234567890', 'amar@amarcell.com', 'Senin-Sabtu: 09:00-18:00');

-- Insert sample data untuk barang
INSERT INTO barang (nama_barang, kategori, stok, harga_modal, harga_jual, deskripsi) VALUES 
('LCD iPhone 7', 'sparepart', 5, 250000, 350000, 'LCD original iPhone 7 berkualitas'),
('Baterai Samsung A50', 'sparepart', 10, 80000, 120000, 'Baterai original Samsung A50'),
('iPhone X 64GB Bekas', 'hp_bekas', 2, 3500000, 4500000, 'iPhone X 64GB kondisi 95%'),
('Samsung S9+ Bekas', 'hp_bekas', 1, 2800000, 3500000, 'Samsung S9+ kondisi bagus');

-- Insert sample data untuk pelanggan
INSERT INTO pelanggan (nama, kontak, email, alamat) VALUES 
('Budi Santoso', '081234567891', 'budi@email.com', 'Jl. Merdeka No. 1'),
('Siti Nurhaliza', '081234567892', 'siti@email.com', 'Jl. Ahmad Yani No. 2');

-- Insert sample data untuk servis
INSERT INTO servis (pelanggan_id, jenis_hp, kerusakan, estimasi_biaya, status) VALUES 
(1, 'iPhone 8', 'LCD retak dan touch tidak berfungsi', 450000, 'dikerjakan'),
(2, 'Samsung A30', 'Baterai bocor dan charging port rusak', 200000, 'menunggu');

-- Insert sample data untuk testimoni
INSERT INTO testimoni (pelanggan_id, isi, rating, status) VALUES 
(1, 'Servisnya cepat dan hasil memuaskan. Terima kasih Amar!', 5, 'diterima'),
(2, 'Pelayanan yang baik dan harga terjangkau.', 4, 'diterima');