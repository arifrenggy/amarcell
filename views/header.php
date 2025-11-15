<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Amar Cell Service' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
     <a href="<?= $whatsapp_link ?>?text=Halo%20<?= urlencode($title) ?>%2C%20saya%20ingin%20bertanya%20tentang%20layanan%20servis%20HP." target="_blank" class="btn btn-success rounded-circle p-3 shadow-lg whatsapp-float" data-bs-toggle="tooltip" data-bs-placement="left" title="Hubungi Kami via WhatsApp">
        <i class="bi bi-whatsapp" style="font-size: 24px;"></i>
    </a>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">
                <i class="bi bi-tools me-2"></i><?= htmlspecialchars($title) ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analisis.php">Analisis Kerusakan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="katalog.php">Katalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="testimoni.php">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kontak.php">Kontak & Profil</a>
                    </li>
                </ul>
                <a href="admin/login.php" class="btn btn-outline-primary btn-sm ms-lg-3 mt-2 mt-lg-0">
                    <i class="bi bi-person-fill me-1"></i>Login Admin
                </a>
            </div>
        </div>
    </nav>