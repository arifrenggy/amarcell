<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barang.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

$products = $barang->getAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=laporan-barang-' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

// Add header row
fputcsv($output, ['ID', 'Nama Barang', 'Kategori', 'Stok', 'Harga Modal', 'Harga Jual', 'Deskripsi', 'Tanggal Dibuat']);

// Add data rows
foreach ($products as $product) {
    fputcsv($output, [
        $product['id'],
        $product['nama_barang'],
        $product['kategori'],
        $product['stok'],
        $product['harga_modal'],
        $product['harga_jual'],
        $product['deskripsi'],
        $product['created_at']
    ]);
}

fclose($output);
exit();
