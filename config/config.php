<?php
// Konfigurasi Umum
session_start();

// Base URL
$base_url = "http://localhost/amarcell/";

// Upload Configuration
$upload_dir = "uploads/";
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];

// Database Configuration
$db_host = "localhost";
$db_name = "amarcell_db";
$db_user = "root";
$db_pass = "";

// Email Configuration (untuk notifikasi)
$email_host = "smtp.gmail.com";
$email_port = 587;
$email_user = "your-email@gmail.com";
$email_pass = "your-app-password";

// WhatsApp Configuration
$whatsapp_api_url = "https://api.whatsapp.com/send";
$whatsapp_number = "6281234567890"; // Format internasional

// Pagination
$items_per_page = 10;

// Status Servis
$status_servis = [
    'menunggu' => 'Menunggu',
    'dikerjakan' => 'Dalam Pengerjaan',
    'selesai' => 'Selesai',
    'diambil' => 'Sudah Diambil'
];

// Status Transaksi
$status_transaksi = [
    'pending' => 'Pending',
    'selesai' => 'Selesai',
    'batal' => 'Batal'
];

// Metode Pembayaran
$metode_pembayaran = [
    'tunai' => 'Tunai',
    'transfer' => 'Transfer Bank',
    'kredit' => 'Kredit'
];

// Kategori Barang
$kategori_barang = [
    'sparepart' => 'Sparepart',
    'hp_bekas' => 'HP Bekas'
];

// Status Testimoni
$status_testimoni = [
    'pending' => 'Menunggu',
    'diterima' => 'Diterima',
    'ditolak' => 'Ditolak'
];

// Fungsi Helper
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function checkLogin() {
    if (!isset($_SESSION['admin_id'])) {
        redirect('admin/login.php');
        exit;
    }
}

function getStatusBadge($status) {
    $badges = [
        'menunggu' => 'warning',
        'dikerjakan' => 'info',
        'selesai' => 'success',
        'diambil' => 'primary',
        'pending' => 'warning',
        'batal' => 'danger'
    ];
    return $badges[$status] ?? 'secondary';
}


function uploadFoto($file) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed)) {
        return '';
    }

    if ($file["size"] > 5 * 1024 * 1024) {
        return '';
    }

    $new_name = uniqid() . "." . $imageFileType;
    $target_file = $target_dir . $new_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $new_name;
    }

    return '';
}
?>