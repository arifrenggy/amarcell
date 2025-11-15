<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Pengaturan.php';
require_once __DIR__ . '/models/GeminiAI.php';

$database = new Database();
$db = $database->getConnection();

$pengaturan = new Pengaturan($db);
$api_key = $pengaturan->getGeminiKey();
$gemini = new GeminiAI($api_key);

$hasil_analisis = null;
$jenis_hp_selected = '';
$kerusakan_selected = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_hp = $_POST['jenis_hp'] ?? '';
    $kerusakan = $_POST['jenis_kerusakan'] ?? '';

    if ($jenis_hp && $kerusakan) {
        $hasil_analisis = $gemini->analyzeDamage($jenis_hp, $kerusakan);
        $jenis_hp_selected = $jenis_hp;
        $kerusakan_selected = $kerusakan;
    }
}

$settings = $pengaturan->getSettings();
$title = $settings['nama_toko'] ?? 'Amar Cell Service';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Analisis Kerusakan HP - <?= htmlspecialchars($title) ?></title>
  <meta name="description" content="Gunakan AI untuk mendapatkan estimasi biaya perbaikan HP Anda secara akurat.">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root {
      --primary: #0f172a;
      --secondary: #1e293b;
      --accent: #d4af37;
      --light: #f8fafc;
      --text: #61e43e;
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
      background: linear-gradient(135deg, var(--primary) 50%, var(--secondary) 100%);
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
      border: 1px solid var(--border);
      transition: all 0.3s ease;
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

    .form-control-modern {
      border-radius: 12px;
      border: 2px solid var(--border);
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control-modern:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .result-card-modern {
      background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
      border: 1px solid var(--border);
    }

    .price-highlight-modern {
      font-size: 2.5rem;
      font-weight: 800;
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

<?php include 'views/header.php'; ?>

<section class="hero-section-modern">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7 animate-fadeInUp">
        <h1 class="hero-title-modern">
          Analisis Kerusakan HP dengan <span class="text-warning">AI</span>
        </h1>
        <p class="hero-subtitle-modern">
          Dapatkan estimasi biaya perbaikan secara otomatis berdasarkan jenis HP dan gejala kerusakan.
        </p>
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
        <i class="bi bi-cpu display-1 text-warning opacity-75"></i>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Form Input -->
      <div class="col-lg-6">
        <div class="modern-card">
          <h4 class="fw-bold mb-4">
            <i class="bi bi-search me-2 text-warning"></i>Pilih HP & Gejala Kerusakan
          </h4>
          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label fw-semibold">Jenis HP</label>
              <select class="form-control-modern form-select" name="jenis_hp" required>
                <option value="">-- Pilih Jenis HP --</option>
                <option value="iPhone" <?= $jenis_hp_selected == 'iPhone' ? 'selected' : '' ?>>iPhone</option>
                <option value="Samsung" <?= $jenis_hp_selected == 'Samsung' ? 'selected' : '' ?>>Samsung</option>
                <option value="Xiaomi" <?= $jenis_hp_selected == 'Xiaomi' ? 'selected' : '' ?>>Xiaomi</option>
                <option value="Vivo" <?= $jenis_hp_selected == 'Vivo' ? 'selected' : '' ?>>Vivo</option>
                <option value="Oppo" <?= $jenis_hp_selected == 'Oppo' ? 'selected' : '' ?>>Oppo</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Deskripsi Kerusakan</label>
              <textarea class="form-control-modern" name="jenis_kerusakan" rows="4" placeholder="Contoh: Layar retak, baterai cepat habis, tidak bisa charge..." required><?= htmlspecialchars($kerusakan_selected) ?></textarea>
            </div>

            <button type="submit" class="btn-modern w-100">Analisis dengan AI</button>
          </form>
        </div>
      </div>

      <!-- Hasil Analisis -->
      <div class="col-lg-6">
        <?php if ($hasil_analisis): ?>
          <div class="result-card-modern animate-fadeInUp">
            <?php if (isset($hasil_analisis['error'])): ?>
              <div class="alert alert-warning"><?= $hasil_analisis['error'] ?></div>
            <?php else: ?>
              <div class="text-center mb-4">
                <i class="bi bi-check-circle-fill text-success display-4"></i>
                <h4 class="fw-bold mt-3">Hasil Analisis AI</h4>
              </div>

              <div class="text-center mb-4">
                <div class="price-highlight-modern">Rp <?= number_format($hasil_analisis['estimasi_biaya'], 0, ',', '.') ?></div>
                <p class="text-muted">Estimasi Biaya Perbaikan</p>
              </div>

              <div class="mb-3">
                <h6 class="fw-bold">Solusi:</h6>
                <p class="text-muted"><?= htmlspecialchars($hasil_analisis['solusi']) ?></p>
              </div>

              <div class="mb-4">
                <h6 class="fw-bold">Waktu Pengerjaan:</h6>
                <p class="text-muted"><?= htmlspecialchars($hasil_analisis['waktu_pengerjaan']) ?></p>
              </div>

              <div class="d-grid gap-2">
                <a href="booking.php?jenis_hp=<?= urlencode($jenis_hp_selected) ?>&kerusakan=<?= urlencode($kerusakan_selected) ?>" class="btn-modern">
                  <i class="bi bi-calendar-plus me-2"></i>Booking Servis Sekarang
                </a>
                <button class="btn-outline-modern" onclick="window.location.href='analisis.php'">
                  <i class="bi bi-arrow-clockwise me-2"></i>Analisis Lainnya
                </button>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="modern-card text-center">
            <i class="bi bi-robot display-1 text-muted"></i>
            <h5 class="mt-3">Hasil Analisis AI Akan Muncul di Sini</h5>
            <p class="text-muted">Silakan pilih jenis HP dan deskripsi kerusakan untuk mendapatkan estimasi biaya perbaikan secara otomatis.</p>
          </div>
        <?php endif; ?>
      </div>
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

  document.querySelectorAll('.modern-card, .result-card-modern').forEach(el => {
    observer.observe(el);
  });
</script>
</body>
</html>