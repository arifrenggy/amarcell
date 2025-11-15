<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barang.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

$id = $_GET['id'] ?? die('ID barang tidak ditemukan.');
$item = $barang->getById($id);

if (!$item) {
    die('Barang tidak ditemukan.');
}

$current_page = 'barang';
$page_title = 'Detail Barang';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><?= htmlspecialchars($item['nama_barang']) ?></h2>
        <div>
            <a href="barang.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
            <a href="barang_edit.php?id=<?= $item['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil me-2"></i>Edit</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?php if (!empty($item['foto'])): ?>
                        <img src="<?= filter_var($item['foto'], FILTER_VALIDATE_URL) ? htmlspecialchars($item['foto']) : '../' . htmlspecialchars($item['foto']) ?>" alt="Foto Barang" class="img-fluid rounded">
                    <?php else: ?>
                        <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center rounded">
                            <i class="bi bi-image" style="font-size: 4rem; color: #ccc;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p class="text-muted"><?= htmlspecialchars($item['deskripsi'] ?? '') ?></p>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Kategori:</strong> <span class="badge bg-info"><?= ucfirst($item['kategori'] ?? '') ?></span></p>
                            <p class="mb-2"><strong>Stok Saat Ini:</strong> <span class="fs-5"><?= htmlspecialchars($item['stok'] ?? '') ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Harga Modal:</strong> Rp <?= number_format($item['harga_modal'] ?? 0, 0, ',', '.') ?></p>
                            <p class="mb-2"><strong>Harga Jual:</strong> <span class="fs-5 text-success">Rp <?= number_format($item['harga_jual'] ?? 0, 0, ',', '.') ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>