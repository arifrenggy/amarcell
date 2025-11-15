<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Transaksi.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$transaksi = new Transaksi($db);

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action === 'delete' && $id > 0) {
    if ($transaksi->delete($id)) {
        $_SESSION['success'] = 'Transaksi berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus transaksi.';
    }
    header('Location: transaksi.php');
    exit();
}

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$transactions = $transaksi->getAll($limit, $offset, $search);
$total_transactions = $transaksi->getTotalCount($search);
$total_pages = ceil($total_transactions / $limit);

$current_page = 'transaksi';
$page_title = 'Data Transaksi';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Transaksi (<?= $total_transactions ?>)</h5>
            <div class="d-flex">
                <form method="get" class="me-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari ID/Pelanggan..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <a href="transaksi_tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Transaksi</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="fw-bold">#TRX<?= str_pad($transaction['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td><?= date('d M Y, H:i', strtotime($transaction['tanggal'])) ?></td>
                                <td><?= htmlspecialchars($transaction['nama_pelanggan'] ?? 'N/A') ?></td>
                                <td class="text-end">Rp <?= number_format($transaction['total'], 0, ',', '.') ?></td>
                                <td><?= ucfirst($transaction['metode_pembayaran']) ?></td>
                                <td><span class="badge rounded-pill bg-light text-dark"><?= ucfirst($transaction['status']) ?></span></td>
                                <td>
                                    <a href="transaksi_detail.php?id=<?= $transaction['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                    <a href="transaksi_edit.php?id=<?= $transaction['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    <a href="transaksi.php?action=delete&id=<?= $transaction['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/footer.php'; ?>
