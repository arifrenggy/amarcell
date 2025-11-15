<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Transaksi.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$transaksi = new Transaksi($db);
$detailTransaksi = new DetailTransaksi($db);

$id = $_GET['id'] ?? die('ID transaksi tidak ditemukan.');
$transaction = $transaksi->getById($id);

if (!$transaction) {
    die('Data transaksi tidak ditemukan.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'metode_pembayaran' => $_POST['metode_pembayaran'],
        'status' => $_POST['status'],
        'keterangan' => $_POST['keterangan'],
        'total' => $transaction['total']
    ];

    if ($transaksi->update($id, $data)) {
        $_SESSION['success'] = 'Data transaksi berhasil diupdate.';
        header('Location: transaksi.php');
        exit();
    } else {
        $error = 'Gagal mengupdate data transaksi.';
    }
}

$transaction_details = $detailTransaksi->getByTransaksiId($id);
$current_page = 'transaksi';
$page_title = 'Edit Transaksi #TRX' . str_pad($transaction['id'], 4, '0', STR_PAD_LEFT);

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?= $page_title ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($transaction['nama_pelanggan']) ?>" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="form-select" required>
                                    <option value="tunai" <?= $transaction['metode_pembayaran'] == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                                    <option value="transfer" <?= $transaction['metode_pembayaran'] == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                                    <option value="qris" <?= $transaction['metode_pembayaran'] == 'qris' ? 'selected' : '' ?>>QRIS</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" <?= $transaction['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="selesai" <?= $transaction['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="batal" <?= $transaction['status'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($transaction['keterangan']) ?></textarea>
                        </div>

                        <h5 class="mt-4">Barang dalam Transaksi (Read-only)</h5>
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Nama</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead>
                            <tbody>
                                <?php foreach($transaction_details as $detail): ?>
                                <tr>
                                    <td><?= htmlspecialchars($detail['nama_barang']) ?></td>
                                    <td><?= $detail['jumlah'] ?></td>
                                    <td>Rp <?= number_format($detail['harga_satuan']) ?></td>
                                    <td>Rp <?= number_format($detail['subtotal']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="transaksi.php" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>