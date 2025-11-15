<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Barang.php';
require_once __DIR__ . '/models/Testimoni.php';
require_once __DIR__ . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();

$barang = new Barang($db);
$testimoni = new Testimoni($db);
$pengaturan = new Pengaturan($db);

// Fetch products
$barang_populer = $barang->getPopular(4);
$barang_terbaru = $barang->getNewest(4);

$testimoni_aktif = $testimoni->getAll('diterima', 6);
$settings = $pengaturan->getSettings();

$avg_rating = $testimoni->getAverageRating();
$rating_count = $testimoni->getTotalCount('diterima');

$title = $settings['nama_toko'] ?? 'Amar Cell Service';
$whatsapp_number_link = preg_replace('/^08/', '628', $settings['nomor_whatsapp'] ?? '6281234567890');
$whatsapp_link = "https://wa.me/{$whatsapp_number_link}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title) ?> - Servis HP, Sparepart & HP Bekas</title>
  <meta name="description" content="Solusi servis HP profesional, sparepart original, dan HP bekas berkualitas.">
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
      padding: 100px 0;
      position: relative;
      overflow: hidden;
    }

    .hero-title-modern {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 1rem;
    }

    .hero-subtitle-modern {
      font-size: 1.25rem;
      margin-bottom: 2rem;
      color: var(--muted);
    }

    .btn-modern {
      background: linear-gradient(135deg, var(--accent) 0%, #b8941f 100%);
      color: #fff;
      padding: 0.75rem 1.75rem;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }

    .btn-modern:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }

    .btn-outline-modern {
      border: 2px solid var(--accent);
      color: var(--accent);
      padding: 0.75rem 1.75rem;
      border-radius: 12px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-outline-modern:hover {
      background: var(--accent);
      color: #fff;
    }

    .modern-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border);
      transition: all 0.3s ease;
    }

    .modern-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
    }

    .section-padding {
      padding: 80px 0;
    }

    .text-accent {
      color: var(--accent);
    }

    .whatsapp-float {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 999;
      transition: transform 0.3s ease;
    }

    .whatsapp-float:hover {
      transform: scale(1.1);
    }

    .navbar-modern {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
    }

    .rating-stars {
      color: var(--accent);
    }

    .animate-fadeInUp {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.8s ease forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 768px) {
      .hero-title-modern {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>

<a href="<?= $whatsapp_link ?>" target="_blank" class="whatsapp-float btn btn-success rounded-circle p-3 shadow-lg">
  <i class="bi bi-whatsapp fs-4"></i>
</a>

<?php include 'views/header.php'; ?>
  </div>
</nav>

<section class="hero-section-modern">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7 animate-fadeInUp">
        <h1 class="hero-title-modern">
          Solusi Servis HP <span class="text-accent">Cepat & Terpercaya</span>
        </h1>
        <p class="hero-subtitle-modern">
          Perbaikan profesional, sparepart original, dan HP bekas berkualitas. Dengan garansi dan estimasi biaya transparan.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="analisis.php" class="btn-modern">Cek Estimasi Biaya</a>
          <a href="booking.php" class="btn-outline-modern">Booking Servis</a>
        </div>
        <div class="mt-4 pt-3 border-top border-secondary">
          <p class="mb-1 fw-bold">
            <i class="bi bi-star-fill rating-stars"></i> <?= number_format($avg_rating, 1) ?> dari 5.0
          </p>
          <small class="text-muted">Berdasarkan <?= $rating_count ?> ulasan pelanggan</small>
        </div>
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
        <img src="assets/img/placeholder-hero.svg" alt="Servis HP" class="img-fluid">
      </div>
    </div>
  </div>
</section>

<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-primary">Layanan Kami</h2>
      <p class="text-muted">Solusi lengkap untuk semua kebutuhan HP Anda</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="modern-card text-center">
          <i class="bi bi-gear-wide-connected text-primary fs-1 mb-3"></i>
          <h5 class="fw-bold">Servis HP Profesional</h5>
          <p class="text-muted">Perbaikan cepat dan bergaransi untuk semua jenis kerusakan HP.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="modern-card text-center">
          <i class="bi bi-boxes text-success fs-1 mb-3"></i>
          <h5 class="fw-bold">Sparepart Original</h5>
          <p class="text-muted">LCD, baterai, kamera, dan lainnya dengan kualitas terbaik.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="modern-card text-center">
          <i class="bi bi-phone text-warning fs-1 mb-3"></i>
          <h5 class="fw-bold">HP Bekas Berkualitas</h5>
          <p class="text-muted">Terverifikasi, siap pakai, dan bergaransi resmi.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-primary">Produk Populer</h2>
      <p class="text-muted">Sparepart dan HP bekas paling laris bulan ini</p>
    </div>
    <div class="row g-4">
      <?php if (count($barang_populer) > 0): ?>
        <?php foreach ($barang_populer as $b): ?>
          <div class="col-lg-3 col-md-6">
            <div class="modern-card">
              <?php
                $foto_url = 'assets/img/placeholder-product.jpg'; // Default placeholder
                if (!empty($b['foto'])) {
                    if (filter_var($b['foto'], FILTER_VALIDATE_URL)) {
                        $foto_url = htmlspecialchars($b['foto']);
                    } else {
                        $foto_url = htmlspecialchars($b['foto']);
                    }
                }
              ?>
              <img src="<?= $foto_url ?>"
                   class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($b['nama_barang']) ?>" style="height: 200px; width: 100%; object-fit: cover;">
              <span class="badge bg-secondary mb-2"><?= ucfirst($b['kategori']) ?></span>
              <h6 class="fw-bold"><?= htmlspecialchars($b['nama_barang']) ?></h6>
              <p class="text-success fw-bold"><?= formatRupiah($b['harga_jual']) ?></p>
              <a href="katalog.php?search=<?= urlencode($b['nama_barang']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">Produk populer akan tampil di sini.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-primary">Produk Terbaru</h2>
      <p class="text-muted">Barang yang baru saja kami tambahkan</p>
    </div>
    <div class="row g-4">
      <?php if (count($barang_terbaru) > 0): ?>
        <?php foreach ($barang_terbaru as $b): ?>
          <div class="col-lg-3 col-md-6">
            <div class="modern-card">
              <?php
                $foto_url = 'assets/img/placeholder-product.jpg'; // Default placeholder
                if (!empty($b['foto'])) {
                    if (filter_var($b['foto'], FILTER_VALIDATE_URL)) {
                        $foto_url = htmlspecialchars($b['foto']);
                    } else {
                        $foto_url = htmlspecialchars($b['foto']);
                    }
                }
              ?>
              <img src="<?= $foto_url ?>"
                   class="img-fluid rounded mb-3" alt="<?= htmlspecialchars($b['nama_barang']) ?>" style="height: 200px; width: 100%; object-fit: cover;">
              <span class="badge bg-secondary mb-2"><?= ucfirst($b['kategori']) ?></span>
              <h6 class="fw-bold"><?= htmlspecialchars($b['nama_barang']) ?></h6>
              <p class="text-success fw-bold"><?= formatRupiah($b['harga_jual']) ?></p>
              <a href="katalog.php?search=<?= urlencode($b['nama_barang']) ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">Produk terbaru akan tampil di sini.</p>
        </div>
      <?php endif; ?>
    </div>
    <div class="text-center mt-5">
      <a href="katalog.php" class="btn-modern">Lihat Semua Produk</a>
    </div>
  </div>
</section>

<section class="section-padding bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold text-primary">Apa Kata Mereka?</h2>
      <p class="text-muted">Testimoni pelanggan yang telah menggunakan layanan kami</p>
    </div>
    <div class="row g-4">
      <?php if (count($testimoni_aktif) > 0): ?>
        <?php foreach ($testimoni_aktif as $t): ?>
          <div class="col-lg-4">
            <div class="modern-card">
              <div class="d-flex mb-3">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="bi bi-star-fill<?= $i <= $t['rating'] ? ' text-warning' : ' text-muted' ?>"></i>
                <?php endfor; ?>
              </div>
              <p class="fst-italic">"<?= htmlspecialchars($t['isi']) ?>"</p>
              <div class="mt-auto pt-3 border-top">
                <p class="mb-0 fw-bold"><?= htmlspecialchars($t['nama_pelanggan'] ?? 'Pelanggan') ?></p>
                <small class="text-muted"><?= date('d M Y', strtotime($t['tanggal'])) ?></small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">Belum ada testimoni yang ditampilkan.</p>
        </div>
      <?php endif; ?>
    </div>
    <div class="text-center mt-5">
      <a href="testimoni.php" class="btn-outline-modern">Lihat Semua Testimoni</a>
    </div>
  </div>
</section>


<?php include 'views/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fadeInUp');
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.modern-card').forEach(card => {
    card.classList.add('animate-fadeInUp');
    observer.observe(card);
  });
</script>
</body>
</html>