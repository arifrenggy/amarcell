<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servis.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$servis = new Servis($db);

$id = $_GET['id'] ?? die('ID servis tidak ditemukan.');
$service = $servis->getById($id);

if (!$service) {
    die('Data servis tidak ditemukan.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'jenis_hp' => $_POST['jenis_hp'],
        'kerusakan' => $_POST['kerusakan'],
        'estimasi_biaya' => $_POST['estimasi_biaya'],
        'status' => $_POST['status'],
        'keterangan' => $_POST['keterangan']
    ];

    if ($data['status'] == 'selesai' && empty($service['tanggal_selesai'])) {
        $data['tanggal_selesai'] = date('Y-m-d H:i:s');
    }

    if ($servis->update($id, $data)) {
        $_SESSION['success'] = 'Data servis berhasil diupdate.';
        header('Location: servis.php');
        exit();
    } else {
        $error = 'Gagal mengupdate data servis.';
    }
}

$current_page = 'servis';
$page_title = 'Edit Servis #SRV' . str_pad($service['id'], 4, '0', STR_PAD_LEFT);

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
                            <input type="text" class="form-control" value="<?= htmlspecialchars($service['nama_pelanggan'] ?? '') ?>" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis HP</label>
                                <input type="text" name="jenis_hp" class="form-control" value="<?= htmlspecialchars($service['jenis_hp'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estimasi Biaya</label>
                                <input type="number" name="estimasi_biaya" class="form-control" value="<?= htmlspecialchars($service['estimasi_biaya'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kerusakan</label>
                            <textarea name="kerusakan" class="form-control" rows="3" required><?= htmlspecialchars($service['kerusakan'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="menunggu" <?= ($service['status'] ?? '') == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="dikerjakan" <?= ($service['status'] ?? '') == 'dikerjakan' ? 'selected' : '' ?>>Dikerjakan</option>
                                <option value="selesai" <?= ($service['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="diambil" <?= ($service['status'] ?? '') == 'diambil' ? 'selected' : '' ?>>Diambil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan / Catatan Admin</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($service['keterangan'] ?? '') ?></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="servis.php" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>