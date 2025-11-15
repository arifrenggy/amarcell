<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Testimoni.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();
$testimoni = new Testimoni($db);

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action === 'update_status' && $id > 0) {
    $status = $_POST['status'] ?? '';
    if ($status && in_array($status, ['diterima', 'ditolak'])) {
        if ($testimoni->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Status testimoni berhasil diupdate.';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate status testimoni.';
        }
    }
    header('Location: testimoni_admin.php');
    exit();
}

if ($action === 'delete' && $id > 0) {
    if ($testimoni->delete($id)) {
        $_SESSION['success'] = 'Testimoni berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus testimoni.';
    }
    header('Location: testimoni_admin.php');
    exit();
}

$status_filter = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$testimonials = $testimoni->getAll($status_filter, $limit, $offset);
$total_testimonials = $testimoni->getTotalCount($status_filter);
$total_pages = ceil($total_testimonials / $limit);

$current_page = 'testimoni';
$page_title = 'Manajemen Testimoni';

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
            <h5 class="mb-0">Daftar Testimoni (<?= $total_testimonials ?>)</h5>
            <div>
                <a href="testimoni_admin.php?status=pending" class="btn btn-sm btn-outline-warning">Pending</a>
                <a href="testimoni_admin.php?status=diterima" class="btn btn-sm btn-outline-success">Diterima</a>
                <a href="testimoni_admin.php?status=ditolak" class="btn btn-sm btn-outline-danger">Ditolak</a>
                <a href="testimoni_admin.php" class="btn btn-sm btn-outline-secondary">Semua</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Pelanggan</th>
                            <th class="text-center">Rating</th>
                            <th>Isi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td><?= htmlspecialchars($testimonial['nama_pelanggan']) ?></td>
                                <td class="text-center"><?= $testimonial['rating'] ?> <i class="bi bi-star-fill text-warning"></i></td>
                                <td><?= htmlspecialchars(substr($testimonial['isi'], 0, 50)) ?>...</td>
                                <td><span class="badge rounded-pill bg-light text-dark"><?= ucfirst($testimonial['status']) ?></span></td>
                                <td>
                                    <?php if ($testimonial['status'] == 'pending'): ?>
                                        <a href="testimoni_admin.php?action=update_status&id=<?= $testimonial['id'] ?>&status=diterima" class="btn btn-sm btn-success" onclick="return confirm('Terima testimoni ini?')"><i class="bi bi-check-lg"></i></a>
                                        <a href="testimoni_admin.php?action=update_status&id=<?= $testimonial['id'] ?>&status=ditolak" class="btn btn-sm btn-warning" onclick="return confirm('Tolak testimoni ini?')"><i class="bi bi-x-lg"></i></a>
                                    <?php endif; ?>
                                    <a href="testimoni_admin.php?action=delete&id=<?= $testimonial['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
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
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&status=<?= $status_filter ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/footer.php'; ?>
