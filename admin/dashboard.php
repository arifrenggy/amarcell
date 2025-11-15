<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Servis.php';
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../models/Transaksi.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$servis = new Servis($db);
$barang = new Barang($db);
$transaksi = new Transaksi($db);

// Ambil data untuk dashboard
$stats = [
    'total_servis' => $servis->getTotalCount(),
    'servis_menunggu' => count($servis->getByStatus('menunggu')),
    'servis_dikerjakan' => count($servis->getByStatus('dikerjakan')),
    'total_barang' => $barang->getTotalCount(),
    'pendapatan_hari_ini' => $transaksi->getTotalRevenue(date('Y-m-d'), date('Y-m-d')),
];

$recent_servis = $servis->getAll(null, 5);
$recent_transaksi = $transaksi->getAll(5);
$stok_menipis = $barang->getStokMenipis();

$current_page = 'dashboard';
$page_title = 'Dashboard';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1"><?= $stats['total_servis'] ?></h4>
                        <p class="text-muted mb-0">Total Servis</p>
                    </div>
                    <div class="ms-3 text-primary fs-2"><i class="bi bi-tools"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1"><?= $stats['servis_menunggu'] ?></h4>
                        <p class="text-muted mb-0">Servis Menunggu</p>
                    </div>
                    <div class="ms-3 text-warning fs-2"><i class="bi bi-clock-history"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1"><?= $stats['servis_dikerjakan'] ?></h4>
                        <p class="text-muted mb-0">Dalam Proses</p>
                    </div>
                    <div class="ms-3 text-info fs-2"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1">Rp <?= number_format($stats['pendapatan_hari_ini'], 0, ',', '.') ?></h4>
                        <p class="text-muted mb-0">Pendapatan Hari Ini</p>
                    </div>
                    <div class="ms-3 text-success fs-2"><i class="bi bi-cash-stack"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Recent Services -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Servis Terbaru</h5>
                    <a href="servis.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php foreach ($recent_servis as $servis_item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($servis_item['jenis_hp']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($servis_item['nama_pelanggan'] ?? 'N/A') ?></small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-light text-dark rounded-pill"><?= ucfirst($servis_item['status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaksi Terbaru</h5>
                    <a href="transaksi.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php foreach ($recent_transaksi as $transaksi_item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($transaksi_item['nama_pelanggan'] ?? 'N/A') ?></strong><br>
                                            <small class="text-muted"><?= date('d M Y', strtotime($transaksi_item['tanggal'])) ?></small>
                                        </td>
                                        <td class="text-end">
                                            <strong>Rp <?= number_format($transaksi_item['total'], 0, ',', '.') ?></strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
