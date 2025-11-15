<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Barang.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_barang' => $_POST['nama_barang'] ?? '',
        'kategori' => $_POST['kategori'] ?? '',
        'stok' => $_POST['stok'] ?? 0,
        'harga_modal' => $_POST['harga_modal'] ?? 0,
        'harga_jual' => $_POST['harga_jual'] ?? 0,
        'deskripsi' => $_POST['deskripsi'] ?? '',
        'foto' => ''
    ];

    if (empty($data['nama_barang']) || empty($data['kategori'])) {
        $error = 'Nama barang dan kategori wajib diisi.';
    } else {
        // Handle image
        $sumber_gambar = $_POST['sumber_gambar'] ?? 'upload';
        if ($sumber_gambar === 'link') {
            $data['foto'] = filter_var($_POST['foto_link'], FILTER_VALIDATE_URL) ? $_POST['foto_link'] : '';
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
                } else {
                    $error = 'Gagal mengupload gambar.';
                }
            } else {
                $error = 'Format file gambar tidak valid. Hanya JPG, JPEG, PNG, GIF, WEBP yang diizinkan.';
            }
        }

        if (empty($error)) {
            if ($barang->create($data)) {
                $_SESSION['success'] = 'Barang baru berhasil ditambahkan.';
                header('Location: barang.php');
                exit();
            } else {
                $error = 'Gagal menambahkan data barang ke database.';
            }
        }
    }
}

$current_page = 'barang';
$page_title = 'Tambah Barang Baru';

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
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="sparepart">Sparepart</option>
                                <option value="hp_bekas">HP Bekas</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" name="stok" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Modal</label>
                                <input type="number" name="harga_modal" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sumber Gambar</label>
                            <select id="sumber_gambar" name="sumber_gambar" class="form-select">
                                <option value="upload">Upload Gambar</option>
                                <option value="link">Link Gambar</option>
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
                            <button type="submit" class="btn btn-primary">Simpan</button>
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