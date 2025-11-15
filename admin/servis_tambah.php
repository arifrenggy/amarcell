<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servis.php';
require_once __DIR__ . '/../models/Pelanggan.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$servis = new Servis($db);
$pelanggan = new Pelanggan($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'pelanggan_id' => $_POST['pelanggan_id'] ?? '',
        'jenis_hp' => $_POST['jenis_hp'] ?? '',
        'kerusakan' => $_POST['kerusakan'] ?? '',
        'estimasi_biaya' => $_POST['estimasi_biaya'] ?? 0,
        'status' => 'menunggu'
    ];

    if (empty($data['pelanggan_id']) || empty($data['jenis_hp']) || empty($data['kerusakan'])) {
        $error = 'Semua field wajib diisi.';
    } else {
        if ($servis->create($data)) {
            $_SESSION['success'] = 'Servis baru berhasil ditambahkan.';
            header('Location: servis.php');
            exit();
        } else {
            $error = 'Gagal menambahkan data servis.';
        }
    }
}

$list_pelanggan = $pelanggan->getAll();
$current_page = 'servis';
$page_title = 'Tambah Servis Baru';

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
                            <select name="pelanggan_id" class="form-select" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($list_pelanggan as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?> - <?= htmlspecialchars($p['kontak']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis HP</label>
                            <input type="text" name="jenis_hp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kerusakan</label>
                            <textarea name="kerusakan" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimasi Biaya</label>
                            <input type="number" name="estimasi_biaya" class="form-control" min="0" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="servis.php" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
