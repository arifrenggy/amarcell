<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servis.php';
require_once __DIR__ . '/../models/Transaksi.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$servis = new Servis($db);
$transaksi = new Transaksi($db);

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Amarcell_$start_date-$end_date.xls");

echo "Laporan Amar Cell Service\n";
echo "Periode: $start_date s/d $end_date\n\n";

echo "Data Servis\n";
echo "ID\tTanggal\tPelanggan\tJenis HP\tKerusakan\tEstimasi\tStatus\n";

$servis_data = $servis->getByDateRange($start_date, $end_date);
foreach ($servis_data as $s) {
    echo $s['id'] . "\t" . $s['tanggal_masuk'] . "\t" . ($s['nama_pelanggan'] ?? 'Pelanggan') . "\t" . $s['jenis_hp'] . "\t" . $s['kerusakan'] . "\t" . $s['estimasi_biaya'] . "\t" . $s['status'] . "\n";
}

echo "\nData Transaksi\n";
echo "ID\tTanggal\tPelanggan\tTotal\tMetode\tStatus\n";

$transaksi_data = $transaksi->getByDateRange($start_date, $end_date);
foreach ($transaksi_data as $t) {
    echo $t['id'] . "\t" . $t['tanggal'] . "\t" . ($t['nama_pelanggan'] ?? 'Pelanggan') . "\t" . $t['total'] . "\t" . $t['metode_pembayaran'] . "\t" . $t['status'] . "\n";
}
?>