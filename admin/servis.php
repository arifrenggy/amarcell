<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Servis.php';

checkLogin();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/servis_error.log');

$database = new Database();
$db = $database->getConnection();
$servis = new Servis($db);

// Handle actions
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if ($action === 'update_status' && $id > 0) {
    $status = $_POST['status'] ?? '';
    if ($status && in_array($status, ['menunggu', 'dikerjakan', 'selesai', 'diambil'])) {
        if ($servis->updateStatus($id, $status)) {
            $_SESSION['success'] = 'Status servis berhasil diupdate';
        } else {
            $_SESSION['error'] = 'Gagal mengupdate status servis';
        }
    }
    header('Location: servis.php');
    exit();
}

if ($action === 'delete' && $id > 0) {
    if ($servis->delete($id)) {
        $_SESSION['success'] = 'Data servis berhasil dihapus';
    } else {
        $_SESSION['error'] = 'Gagal menghapus data servis';
    }
    header('Location: servis.php');
    exit();
}

$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 15;
$offset = ($page - 1) * $limit;

$services = $servis->getAll($status_filter, $limit, $offset, $search);
$total_services = $servis->getTotalCount($status_filter, $search);
$total_pages = ceil($total_services / $limit);



$current_page = 'servis';
$page_title = 'Data Servis';

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
            <h5 class="mb-0">Daftar Servis (<?= $total_services ?>)</h5>
            <div class="d-flex">
                <form method="get" class="me-2">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <a href="servis_tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Servis</a>
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
                            <th>Jenis HP</th>
                            <th>Kerusakan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($services)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data servis.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($services as $service): ?>
                                <tr>
                                    <td class="fw-bold">#SRV<?= str_pad($service['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?= date('d M Y', strtotime($service['tanggal_masuk'])) ?></td>
                                    <td><?= htmlspecialchars($service['nama_pelanggan'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($service['jenis_hp']) ?></td>
                                    <td><?= htmlspecialchars(substr($service['kerusakan'], 0, 30)) ?>...</td>
                                    <td><span class="badge rounded-pill bg-<?= getStatusBadge($service['status']) ?>"><?= ucfirst($service['status']) ?></span></td>
                                    <td>
                                        <a href="servis_detail.php?id=<?= $service['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                        <a href="servis_edit.php?id=<?= $service['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                        <a href="servis.php?action=delete&id=<?= $service['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($total_pages > 1): ?>
        <div class="card-footer bg-white">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>&status=<?= $status_filter ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/footer.php'; ?>
