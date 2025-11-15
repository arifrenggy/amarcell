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

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_barang' => $_POST['nama_barang'],
        'kategori' => $_POST['kategori'],
        'stok' => $_POST['stok'],
        'harga_modal' => $_POST['harga_modal'],
        'harga_jual' => $_POST['harga_jual'],
        'deskripsi' => $_POST['deskripsi']
    ];

    // Handle image update
    $sumber_gambar = $_POST['sumber_gambar'] ?? 'upload';
    $foto_updated = false;

    if ($sumber_gambar === 'link' && !empty($_POST['foto_link'])) {
        if (filter_var($_POST['foto_link'], FILTER_VALIDATE_URL)) {
            $data['foto'] = $_POST['foto_link'];
            $foto_updated = true;
        } else {
            $error = 'Link gambar tidak valid.';
        }
    } elseif (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/barang/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_info = pathinfo($_FILES['foto_upload']['name']);
        $file_ext = strtolower($file_info['extension']);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_filename = uniqid('barang_', true) . '.' . $file_ext;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $target_file)) {
                $data['foto'] = 'uploads/barang/' . $new_filename;
                $foto_updated = true;
            } else {
                $error = 'Gagal mengupload gambar baru.';
            }
        } else {
            $error = 'Format file gambar tidak valid. Hanya JPG, JPEG, PNG, GIF, WEBP yang diizinkan.';
        }
    }

    if (empty($error)) {
        if ($barang->update($id, $data)) {
            // If photo was updated and old photo was a local file, delete it
            if ($foto_updated && !empty($item['foto']) && !filter_var($item['foto'], FILTER_VALIDATE_URL)) {
                $old_foto_path = __DIR__ . '/../' . $item['foto'];
                if (file_exists($old_foto_path)) {
                    unlink($old_foto_path);
                }
            }
            $_SESSION['success'] = 'Data barang berhasil diupdate.';
            header('Location: barang.php');
            exit();
        } else {
            $error = 'Gagal mengupdate data barang.';
        }
    }
}

$current_page = 'barang';
$page_title = 'Edit Barang';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?= $page_title ?>: <?= htmlspecialchars($item['nama_barang']) ?></h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($item['nama_barang'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="sparepart" <?= ($item['kategori'] ?? '') == 'sparepart' ? 'selected' : '' ?>>Sparepart</option>
                                <option value="hp_bekas" <?= ($item['kategori'] ?? '') == 'hp_bekas' ? 'selected' : '' ?>>HP Bekas</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($item['stok'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Modal</label>
                                <input type="number" name="harga_modal" class="form-control" value="<?= htmlspecialchars($item['harga_modal'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" value="<?= htmlspecialchars($item['harga_jual'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($item['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <div>
                                <?php if (!empty($item['foto'])): ?>
                                    <img src="<?= filter_var($item['foto'], FILTER_VALIDATE_URL) ? htmlspecialchars($item['foto']) : '../' . htmlspecialchars($item['foto']) ?>" alt="Foto Barang" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                                <?php else: ?>
                                    <p class="text-muted">Tidak ada gambar.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ubah Gambar (Opsional)</label>
                            <select id="sumber_gambar" name="sumber_gambar" class="form-select">
                                <option value="upload">Upload Gambar Baru</option>
                                <option value="link">Gunakan Link Gambar</option>
                            </select>
                        </div>
                        <div id="upload_gambar_div" class="mb-3">
                            <label class="form-label">Upload Gambar</label>
                            <input type="file" name="foto_upload" class="form-control">
                        </div>
                        <div id="link_gambar_div" class="mb-3" style="display: none;">
                            <label class="form-label">Link Gambar</label>
                            <input type="text" name="foto_link" class="form-control" placeholder="https://example.com/gambar.jpg">
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="barang.php" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sumber_gambar').addEventListener('change', function () {
    if (this.value === 'upload') {
        document.getElementById('upload_gambar_div').style.display = 'block';
        document.getElementById('link_gambar_div').style.display = 'none';
    } else {
        document.getElementById('upload_gambar_div').style.display = 'none';
        document.getElementById('link_gambar_div').style.display = 'block';
    }
});
</script>

<?php include 'views/footer.php'; ?>
