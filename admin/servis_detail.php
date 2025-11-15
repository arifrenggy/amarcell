<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servis.php';
require_once __DIR__ . '/../models/Pengaturan.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$servis = new Servis($db);
$pengaturan = new Pengaturan($db);

$id = $_GET['id'] ?? die('ID servis tidak ditemukan.');
$service = $servis->getById($id);
$settings = $pengaturan->getSettings();

if (!$service) {
    die('Data servis tidak ditemukan.');
}

$current_page = 'servis';
$page_title = 'Detail Servis #SRV' . str_pad($service['id'], 4, '0', STR_PAD_LEFT);

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div></div>
        <div>
            <a href="servis.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
            <a href="servis_edit.php?id=<?= $service['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i>Cetak</button>
        </div>
    </div>

    <div class="card" id="receipt">
        <div class="card-body p-4">
            <div class="text-center border-bottom pb-3 mb-3">
                <h3 class="fw-bold mb-1"><?= htmlspecialchars($settings['nama_toko']) ?></h3>
                <p class="text-muted mb-1"><?= htmlspecialchars($settings['alamat']) ?></p>
                <p class="text-muted mb-0">Telp: <?= htmlspecialchars($settings['nomor_whatsapp']) ?></p>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <strong>No. Servis:</strong> <?= $page_title ?><br>
                    <strong>Tanggal Masuk:</strong> <?= date('d M Y H:i', strtotime($service['tanggal_masuk'])) ?>
                </div>
                <div class="col-6 text-end">
                    <strong>Pelanggan:</strong><br>
                    <?= htmlspecialchars($service['nama_pelanggan'] ?? '') ?><br>
                    <?= htmlspecialchars($service['kontak'] ?? '') ?>
                </div>
            </div>

            <table class="table table-bordered">
                <tr><th style="width:25%">Jenis HP</th><td><?= htmlspecialchars($service['jenis_hp']) ?></td></tr>
                <tr><th>Kerusakan</th><td><?= nl2br(htmlspecialchars($service['kerusakan'])) ?></td></tr>
            </table>
            
            <div class="row mt-4">
                <div class="col-7">
                    <p class="text-muted small">
                        <strong>Syarat & Ketentuan:</strong><br>
                        - Barang yang sudah diservis dan tidak diambil dalam 30 hari bukan tanggung jawab kami.<br>
                        - Garansi servis berlaku 7 hari setelah tanggal pengambilan.
                    </p>
                </div>
                <div class="col-5">
                    <div class="text-end">
                        <p class="mb-2">Estimasi Biaya:</p>
                        <h4 class="fw-bold">Rp <?= number_format($service['estimasi_biaya'], 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>