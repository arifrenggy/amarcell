<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Testimoni.php';
require_once __DIR__ . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();

$testimoni = new Testimoni($db);
$pengaturan = new Pengaturan($db);

$settings = $pengaturan->getSettings();

// Handle testimoni submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $isi = sanitizeInput($_POST['isi'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    
    if (empty($nama) || empty($isi) || $rating < 1 || $rating > 5) {
        $error = 'Semua field harus diisi dan rating minimal 1 bintang';
    } else {
        // Create pelanggan dummy untuk testimoni (tanpa login)
        $pelanggan_id = 1; // ID dummy untuk testimoni publik
        
        $result = $testimoni->create([
            'pelanggan_id' => $pelanggan_id,
            'nama_pelanggan' => $nama, // Pass the name directly
            'isi' => $isi,
            'rating' => $rating,
            'status' => 'pending' // Menunggu persetujuan admin
        ]);
        
        if ($result) {
            $success = true;
        } else {
            $error = 'Gagal mengirim testimoni. Silakan coba lagi.';
        }
    }
}

// Get approved testimonials
$testimonials = $testimoni->getAll('diterima', 10);
$average_rating = $testimoni->getAverageRating();
$total_testimonials = $testimoni->getTotalCount('diterima');

$title = $settings['nama_toko'] ?? 'Amar Cell Service';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Pelanggan - <?= htmlspecialchars($title) ?></title>
    <meta name="description" content="Baca testimoni pelanggan Amar Cell Service dan berikan ulasan Anda tentang layanan servis HP profesional kami.">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
    :root {
      --primary: #0f172a;
      --secondary: #1e293b;
      --accent: #d4af37;
      --light: #f8fafc;
      --text: #e2e8f0;
      --muted: #94a3b8;
      --border: #334155;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--light);
      color: var(--primary);
      line-height: 1.6;
    }

    .hero-section-modern {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      color: var(--text);
      padding: 80px 0;
      position: relative;
      overflow: hidden;
    }

    .hero-title-modern {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .hero-subtitle-modern {
      font-size: 1.1rem;
      color: var(--muted);
    }

    .modern-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
      border: 1px solid #e2e8f0;
      transition: all 0.3s ease;
      height: 100%;
    }

    .modern-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
    }

    .btn-modern {
      background: linear-gradient(135deg, var(--accent) 0%, #b8941f 100%);
      color: #fff;
      border: none;
      padding: 0.75rem 1.75rem;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    
    .form-control-modern {
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control-modern:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .rating-stars {
        color: #fbbf24;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        gap: 0.5rem;
        justify-content: center;
        margin: 1rem 0;
    }
    
    .star-rating input {
        display: none;
    }
    
    .star-rating label {
        font-size: 2rem;
        color: #d1d5db;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #fbbf24;
    }

    .success-card-modern {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #fff;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
      text-align: center;
    }
  </style>
</head>
<body>
<?php include 'views/header.php'; ?>

    <section class="hero-section-modern">
        <div class="container text-center">
            <h1 class="hero-title-modern"><i class="bi bi-star-half me-3"></i>Testimoni Pelanggan</h1>
            <p class="hero-subtitle-modern">Lihat apa kata mereka yang telah mempercayakan servis HP kepada kami.</p>
            <div class="d-flex justify-content-center align-items-center gap-2 mt-4">
                <div class="rating-stars fs-4">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star<?= $i <= round($average_rating) ? '-fill' : '' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="fw-bold fs-5"><?= number_format($average_rating, 1) ?></span>
                <span class="text-muted">/ 5 dari <?= $total_testimonials ?> ulasan</span>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="modern-card">
                        <h4 class="fw-bold mb-4">Distribusi Rating</h4>
                        <?php 
                        $rating_distribution = $testimoni->getRatingDistribution();
                        $total_dist_rating = 0;
                        foreach($rating_distribution as $dist) $total_dist_rating += $dist['jumlah'];
                        ?>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <?php 
                            $count = 0;
                            foreach ($rating_distribution as $dist) {
                                if ($dist['rating'] == $i) {
                                    $count = $dist['jumlah'];
                                    break;
                                }
                            }
                            $percentage = $total_dist_rating > 0 ? ($count / $total_dist_rating) * 100 : 0;
                            ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-nowrap me-2" style="width: 60px;"><?= $i ?> Bintang</div>
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-muted ms-2" style="width: 40px; text-align: right;"><?= $count ?></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="modern-card">
                        <h4 class="fw-bold mb-4">Keunggulan Kami</h4>
                        <div class="row g-3">
                            <div class="col-6 text-center">
                                <i class="bi bi-people-fill display-6 text-primary"></i>
                                <h5 class="fw-bold mt-2 mb-1">1000+</h5>
                                <p class="text-muted small mb-0">Pelanggan Puas</p>
                            </div>
                            <div class="col-6 text-center">
                                <i class="bi bi-tools display-6 text-success"></i>
                                <h5 class="fw-bold mt-2 mb-1">95%</h5>
                                <p class="text-muted small mb-0">Tingkat Keberhasilan</p>
                            </div>
                            <div class="col-6 text-center">
                                <i class="bi bi-clock display-6 text-warning"></i>
                                <h5 class="fw-bold mt-2 mb-1">2 Jam</h5>
                                <p class="text-muted small mb-0">Rata-rata Servis</p>
                            </div>
                            <div class="col-6 text-center">
                                <i class="bi bi-shield-check display-6 text-info"></i>
                                <h5 class="fw-bold mt-2 mb-1">30 Hari</h5>
                                <p class="text-muted small mb-0">Garansi Penuh</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <!-- Testimonials Grid -->
            <?php if (empty($testimonials)): ?>
                <div class="modern-card text-center py-5">
                    <i class="bi bi-chat-dots display-1 text-muted"></i>
                    <h4 class="mt-3 mb-2">Belum Ada Testimoni</h4>
                    <p class="text-muted mb-4">Jadilah yang pertama memberikan ulasan!</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="modern-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <h4 class="m-0"><?= strtoupper(substr($testimonial['nama_pelanggan'], 0, 1)) ?></h4>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fw-bold m-0"><?= htmlspecialchars($testimonial['nama_pelanggan']) ?></h5>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $testimonial['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted">"<?= htmlspecialchars($testimonial['isi']) ?>"</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Add Testimonial Form -->
    <section id="form-testimoni" class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if ($success): ?>
                        <div class="success-card-modern">
                            <i class="bi bi-check-circle-fill display-4 mb-3"></i>
                            <h3 class="fw-bold mb-3">Terima Kasih!</h3>
                            <p class="lead mb-4">Testimoni Anda telah kami terima dan akan segera kami proses.</p>
                            <a href="testimoni.php" class="btn btn-light">Kembali ke Testimoni</a>
                        </div>
                    <?php else: ?>
                        <div class="modern-card">
                            <h2 class="fw-bold text-center mb-4">Beri Ulasan</h2>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                            <form method="POST" action="testimoni.php#form-testimoni">
                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-semibold">Nama Anda</label>
                                    <input type="text" class="form-control-modern" id="nama" name="nama" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold d-block text-center">Rating Anda</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 bintang"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 bintang"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 bintang"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 bintang"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 bintang"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="isi" class="form-label fw-semibold">Ulasan Anda</label>
                                    <textarea class="form-control-modern" id="isi" name="isi" rows="4" required></textarea>
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn-modern btn-lg">Kirim Ulasan</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: var(--primary);">
        <div class="container text-center text-white">
            <h2 class="fw-bold mb-4">Puas dengan Layanan Kami?</h2>
            <p class="lead mb-5">Bagikan pengalaman Anda dan bantu pelanggan lain memilih layanan terbaik.</p>
            <a href="#form-testimoni" class="btn-modern">
                <i class="bi bi-pencil-square me-2"></i>Berikan Testimoni
            </a>
        </div>
    </section>

<?php include 'views/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>