<?php
// This sidebar should be self-sufficient in getting notification counts.
if (!isset($db)) {
    // Ensure DB connection is available, but don't re-create if already exists
    if (class_exists('Database')) {
        $database_sidebar = new Database();
        $db = $database_sidebar->getConnection();
    } else {
        // Fallback or error, as Database class should be available
        // For now, we'll suppress errors if it's not mission-critical
    }
}

// Safely include and instantiate Testimoni model
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