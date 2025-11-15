<?php
// This is the main template file for the admin panel.
// It includes the header, CSS, sidebar, and top navbar.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= $page_title ?? 'Amar Cell' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* Core Layout Styles */
        body {
            background-color: #f8fafc;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding-top: 70px; /* Space for the fixed navbar */
        }
        .navbar-top {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-left: 250px;
            position: fixed; 
            width: calc(100% - 250px); 
            z-index: 999;
            top: 0;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #2563eb;
            color: white;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            border: none;
        }
    </style>
</head>
<body>

<?php
// Self-sufficient sidebar logic
if (!isset($db)) {
    $database_sidebar = new Database();
    $db = $database_sidebar->getConnection();
}
if (file_exists(__DIR__ . '/../../models/Testimoni.php')) {
    require_once __DIR__ . '/../../models/Testimoni.php';
    if (isset($db) && class_exists('Testimoni')) {
        $testimoni_sidebar = new Testimoni($db);
        $pending_count = $testimoni_sidebar->getPendingCount();
    } else {
        $pending_count = 0;
    }
} else {
    $pending_count = 0;
}
?>
<div class="sidebar">
    <div class="p-4">
        <a href="dashboard.php" class="text-decoration-none">
            <h4 class="text-white mb-4">
                <i class="bi bi-phone-fill me-2"></i>
                Amar Cell
            </h4>
        </a>
        <nav class="nav flex-column">
            <a class="nav-link <?= ($current_page ?? '') === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'servis' ? 'active' : '' ?>" href="servis.php">
                <i class="bi bi-tools me-2"></i>Data Servis
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'barang' ? 'active' : '' ?>" href="barang.php">
                <i class="bi bi-box-seam me-2"></i>Data Barang
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'transaksi' ? 'active' : '' ?>" href="transaksi.php">
                <i class="bi bi-cash-coin me-2"></i>Transaksi
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'pelanggan' ? 'active' : '' ?>" href="pelanggan.php">
                <i class="bi bi-people me-2"></i>Data Pelanggan
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'testimoni' ? 'active' : '' ?>" href="testimoni_admin.php">
                <i class="bi bi-star me-2"></i>Testimoni
                <?php if ($pending_count > 0): ?>
                    <span class="badge bg-danger ms-auto"><?= $pending_count ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'laporan' ? 'active' : '' ?>" href="laporan.php">
                <i class="bi bi-file-earmark-text me-2"></i>Laporan
            </a>
            <a class="nav-link <?= ($current_page ?? '') === 'pengaturan' ? 'active' : '' ?>" href="pengaturan.php">
                <i class="bi bi-gear me-2"></i>Pengaturan
            </a>
            <hr class="text-white-50">
            <a class="nav-link text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </a>
        </nav>
    </div>
</div>

<nav class="navbar-top navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <h5 class="mb-0"><?= $page_title ?? 'Admin Panel' ?></h5>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="main-content">
