<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Admin - Amar Cell Service' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding-top: 20px;
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
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .popup-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="px-3">
            <h4 class="text-white mb-4">
                <i class="bi bi-phone-fill me-2"></i>Amar Cell Service
            </h4>
            <nav class="nav flex-column">
                <a class="nav-link <?= $current_page == 'dashboard' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link <?= $current_page == 'servis' ? 'active' : '' ?>" href="servis.php">
                    <i class="bi bi-tools me-2"></i>Data Servis
                </a>
                <a class="nav-link <?= $current_page == 'barang' ? 'active' : '' ?>" href="barang.php">
                    <i class="bi bi-box-seam me-2"></i>Data Barang
                </a>
                <a class="nav-link <?= $current_page == 'transaksi' ? 'active' : '' ?>" href="transaksi.php">
                    <i class="bi bi-cash-coin me-2"></i>Transaksi
                </a>
                <a class="nav-link <?= $current_page == 'pelanggan' ? 'active' : '' ?>" href="pelanggan.php">
                    <i class="bi bi-people me-2"></i>Data Pelanggan
                </a>
                <a class="nav-link <?= $current_page == 'testimoni' ? 'active' : '' ?>" href="testimoni_admin.php">
                    <i class="bi bi-star me-2"></i>Testimoni
                </a>
                <a class="nav-link <?= $current_page == 'laporan' ? 'active' : '' ?>" href="laporan.php">
                    <i class="bi bi-file-earmark-text me-2"></i>Laporan
                </a>
                <a class="nav-link <?= $current_page == 'pengaturan' ? 'active' : '' ?>" href="pengaturan.php">
                    <i class="bi bi-gear me-2"></i>Pengaturan
                </a>
                <hr class="text-white-50">
                <a class="nav-link text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="popup-card">
            <?= $content ?>
        </div>
    </div>
</body>
</html>