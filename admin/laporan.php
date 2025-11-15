<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Servis.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$transaksi = new Transaksi($db);
$servis = new Servis($db);

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$transaksi_data = $transaksi->getByDateRange($start_date, $end_date);
$servis_data = $servis->getByDateRange($start_date, $end_date);

$total_pendapatan_transaksi = array_sum(array_column($transaksi_data, 'total'));
$total_pendapatan_servis = array_sum(array_column($servis_data, 'estimasi_biaya'));
$total_pendapatan = $total_pendapatan_transaksi + $total_pendapatan_servis;

$current_page = 'laporan';
$page_title = 'Laporan';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Pendapatan Penjualan</h6>
                    <h4 class="fw-bold">Rp <?= number_format($total_pendapatan_transaksi, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Pendapatan Servis</h6>
                    <h4 class="fw-bold">Rp <?= number_format($total_pendapatan_servis, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white text-center">
                <div class="card-body">
                    <h6 class="text-white-75">Total Pendapatan</h6>
                    <h4 class="fw-bold">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Laporan Transaksi Penjualan</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th class="text-end">Total</th></tr></thead>
                        <tbody>
                            <?php foreach($transaksi_data as $t): ?>
                            <tr><td>#TRX<?= $t['id'] ?></td><td><?= date('d/m/y', strtotime($t['tanggal'])) ?></td><td><?= $t['nama_pelanggan'] ?></td><td class="text-end">Rp <?= number_format($t['total']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">Laporan Servis</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th class="text-end">Biaya</th></tr></thead>
                        <tbody>
                            <?php foreach($servis_data as $s): ?>
                            <tr><td>#SRV<?= $s['id'] ?></td><td><?= date('d/m/y', strtotime($s['tanggal_masuk'])) ?></td><td><?= $s['nama_pelanggan'] ?></td><td class="text-end">Rp <?= number_format($s['estimasi_biaya']) ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
