<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pelanggan.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$pelanggan = new Pelanggan($db);

// Handle Actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action === 'delete' && $id > 0) {
    if ($pelanggan->delete($id)) {
        $_SESSION['success'] = 'Data pelanggan berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus data pelanggan.';
    }
    header('Location: pelanggan.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama' => $_POST['nama'] ?? '',
        'kontak' => $_POST['kontak'] ?? '',
        'email' => $_POST['email'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
    ];

    if ($action === 'add') {
        if ($pelanggan->create($data)) {
            $_SESSION['success'] = 'Pelanggan baru berhasil ditambahkan.';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan pelanggan.';
        }
    } elseif ($action === 'edit' && $id > 0) {
        if ($pelanggan->update($id, $data)) {
            $_SESSION['success'] = 'Data pelanggan berhasil diupdate.';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate data pelanggan.';
        }
    }
    header('Location: pelanggan.php');
    exit();
}

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$list_pelanggan = $pelanggan->getAll($limit, $offset, $search);
$total_pelanggan = $pelanggan->getTotalCount($search);
$total_pages = ceil($total_pelanggan / $limit);

$current_page = 'pelanggan';
$page_title = 'Data Pelanggan';

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

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pelanggan (<?= $total_pelanggan ?>)</h5>
            <div class="d-flex">
                <form method="get" class="me-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama/kontak..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pelangganModal" data-action="add">
                    <i class="bi bi-plus-circle me-1"></i> Tambah
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Email</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list_pelanggan as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nama']) ?></td>
                                <td><?= htmlspecialchars($p['kontak']) ?></td>
                                <td><?= htmlspecialchars($p['email'] ?? '-') ?></td>
                                <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#pelangganModal" data-action="edit" data-id="<?= $p['id'] ?>" data-nama="<?= htmlspecialchars($p['nama']) ?>" data-kontak="<?= htmlspecialchars($p['kontak']) ?>" data-email="<?= htmlspecialchars($p['email']) ?>" data-alamat="<?= htmlspecialchars($p['alamat']) ?>"><i class="bi bi-pencil"></i></button>
                                    <a href="pelanggan.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')"><i class="bi bi-trash"></i></a>
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

<!-- Modal -->
<div class="modal fade" id="pelangganModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pelangganForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="pelangganModalLabel">Tambah Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kontak</label>
                        <input type="text" class="form-control" id="kontak" name="kontak" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('pelangganModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        const form = document.getElementById('pelangganForm');
        const modalTitle = modal.querySelector('.modal-title');
        
        if (action === 'add') {
            modalTitle.textContent = 'Tambah Pelanggan';
            form.action = 'pelanggan.php?action=add';
            form.reset();
        } else if (action === 'edit') {
            modalTitle.textContent = 'Edit Pelanggan';
            const id = button.getAttribute('data-id');
            form.action = `pelanggan.php?action=edit&id=${id}`;
            document.getElementById('nama').value = button.getAttribute('data-nama');
            document.getElementById('kontak').value = button.getAttribute('data-kontak');
            document.getElementById('email').value = button.getAttribute('data-email');
            document.getElementById('alamat').value = button.getAttribute('data-alamat');
        }
    });
});
</script>

<?php include 'views/footer.php'; ?>