<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pengaturan.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$pengaturan = new Pengaturan($db);
$settings = $pengaturan->getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_toko' => $_POST['nama_toko'],
        'alamat' => $_POST['alamat'],
        'nomor_whatsapp' => $_POST['nomor_whatsapp'],
        'email_bisnis' => $_POST['email_bisnis'],
        'jam_buka' => $_POST['jam_buka'],
        'gemini_api_key' => $_POST['gemini_api_key'] ?? ''
    ];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/logo/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if($check !== false) {
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $_SESSION['error'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan.";
            } else {
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                    $data['logo'] = "uploads/logo/" . basename($_FILES["logo"]["name"]);
                } else {
                    $_SESSION['error'] = "Maaf, terjadi error saat mengupload file.";
                }
            }
        } else {
            $_SESSION['error'] = "File bukan gambar.";
        }
    }

    if ($pengaturan->update($data)) {
        $_SESSION['success'] = 'Pengaturan berhasil disimpan.';
    } else {
        if (!isset($_SESSION['error'])) {
            $_SESSION['error'] = 'Gagal menyimpan pengaturan.';
        }
    }
    
    header('Location: pengaturan.php');
    exit();
}

$current_page = 'pengaturan';
$page_title = 'Pengaturan Sistem';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Informasi Toko & Integrasi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" value="<?= htmlspecialchars($settings['nama_toko']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Logo Toko</label>
                            <?php if (!empty($settings['logo'])): ?>
                                <img src="../<?= htmlspecialchars($settings['logo']) ?>" alt="Logo" class="img-thumbnail mb-2" style="max-height: 80px;">
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control">
                            <small class="text-muted">Upload logo baru untuk mengganti. (Format: JPG, PNG, GIF)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" required><?= htmlspecialchars($settings['alamat']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input type="text" name="nomor_whatsapp" class="form-control" value="<?= htmlspecialchars($settings['nomor_whatsapp']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Bisnis</label>
                            <input type="email" name="email_bisnis" class="form-control" value="<?= htmlspecialchars($settings['email_bisnis']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Buka</label>
                            <input type="text" name="jam_buka" class="form-control" value="<?= htmlspecialchars($settings['jam_buka']) ?>">
                        </div>
                        <hr>
                        <h5 class="card-title mb-3">Integrasi</h5>
                        <div class="mb-3">
                            <label class="form-label">Gemini API Key</label>
                            <input type="password" name="gemini_api_key" class="form-control" value="<?= htmlspecialchars($settings['gemini_api_key'] ?? '') ?>" placeholder="Masukkan API Key dari Google AI Studio">
                            <small class="text-muted">Dapatkan dari <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a> untuk fitur Analisis AI.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                 <div class="card-header bg-white">
                    <h5 class="mb-0">Database</h5>
                </div>
                 <div class="card-body">
                     <p>Backup seluruh data database (servis, barang, transaksi, dll) ke dalam sebuah file SQL.</p>
                     <a href="backup.php" class="btn btn-success">
                         <i class="bi bi-database-down me-2"></i>Backup Database
                     </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/footer.php'; ?>
