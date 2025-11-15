<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Pengaturan.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$transaksi = new Transaksi($db);
$detailTransaksi = new DetailTransaksi($db);
$pengaturan = new Pengaturan($db);

$id = $_GET['id'] ?? die('ID transaksi tidak ditemukan.');
$transaction = $transaksi->getById($id);
$settings = $pengaturan->getSettings();

if (!$transaction) {
    die('Data transaksi tidak ditemukan.');
}

$transaction_details = $detailTransaksi->getByTransaksiId($id);
$current_page = 'transaksi';
$page_title = 'Detail Transaksi #TRX' . str_pad($transaction['id'], 4, '0', STR_PAD_LEFT);

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div></div>
        <div>
            <a href="transaksi.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
            <a href="transaksi_edit.php?id=<?= $transaction['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-2"></i>Cetak Struk</button>
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
                    <strong>No. Transaksi:</strong> <?= $page_title ?><br>
                    <strong>Tanggal:</strong> <?= date('d M Y H:i', strtotime($transaction['tanggal'])) ?>
                </div>
                <div class="col-6 text-end">
                    <strong>Pelanggan:</strong><br>
                    <?= htmlspecialchars($transaction['nama_pelanggan']) ?><br>
                    <?= htmlspecialchars($transaction['kontak']) ?>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr class="table-light">
                        <th>Nama Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transaction_details as $detail): ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['nama_barang']) ?></td>
                        <td class="text-center"><?= $detail['jumlah'] ?></td>
                        <td class="text-end">Rp <?= number_format($detail['harga_satuan'], 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end border-top pt-3">Total</td>
                        <td class="text-end border-top pt-3 fs-5">Rp <?= number_format($transaction['total'], 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <div class="text-center mt-4 pt-3 border-top">
                <p class="text-muted">Terima kasih telah berbelanja!</p>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>