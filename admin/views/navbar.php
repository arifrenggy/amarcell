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