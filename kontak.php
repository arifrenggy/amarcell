<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();

$pengaturan = new Pengaturan($db);
$settings = $pengaturan->getSettings();

$title = $settings['nama_toko'] ?? 'Amar Cell Service';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak & Profil - <?= htmlspecialchars($title) ?></title>
    <meta name="description" content="Hubungi Amar Cell Service untuk servis HP profesional, sparepart berkualitas, dan HP bekas terpercaya.">
    
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

    .map-container {
        border-radius: 12px;
        overflow: hidden;
        height: 400px;
    }
    </style>
</head>
<body>
<?php include 'views/header.php'; ?>

    <section class="hero-section-modern">
        <div class="container text-center">
            <h1 class="hero-title-modern"><i class="bi bi-person-rolodex me-3"></i>Hubungi Kami</h1>
            <p class="hero-subtitle-modern">Kami siap membantu Anda dengan layanan servis profesional dan responsif.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="modern-card text-center">
                        <i class="bi bi-whatsapp display-4 text-success mb-3"></i>
                        <h4 class="fw-bold">WhatsApp</h4>
                        <p class="text-muted">Cara tercepat untuk konsultasi dan booking.</p>
                        <a href="https://wa.me/<?= htmlspecialchars($settings['nomor_whatsapp'] ?? '') ?>" class="btn btn-success">Chat Sekarang</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="modern-card text-center">
                        <i class="bi bi-telephone-fill display-4 text-primary mb-3"></i>
                        <h4 class="fw-bold">Telepon</h4>
                        <p class="text-muted">Hubungi kami langsung di jam kerja.</p>
                        <a href="tel:<?= htmlspecialchars($settings['nomor_whatsapp'] ?? '') ?>" class="btn btn-primary">Telepon Sekarang</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="modern-card text-center">
                        <i class="bi bi-envelope-fill display-4 text-danger mb-3"></i>
                        <h4 class="fw-bold">Email</h4>
                        <p class="text-muted">Untuk pertanyaan umum atau kerjasama.</p>
                        <a href="mailto:<?= htmlspecialchars($settings['email_bisnis'] ?? '') ?>" class="btn btn-danger">Kirim Email</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About & Services Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="modern-card">
                        <h4 class="fw-bold mb-4">Tentang Kami</h4>
                        <p class="text-muted">Kami adalah penyedia layanan servis HP profesional dengan pengalaman bertahun-tahun. Kami berkomitmen untuk memberikan solusi terbaik untuk semua masalah perangkat Anda dengan cepat, transparan, dan bergaransi.</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">5+ Tahun Pengalaman</span>
                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill">1000+ HP Diperbaiki</span>
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Garansi 30 Hari</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="modern-card">
                        <h4 class="fw-bold mb-4">Layanan Utama Kami</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Servis Hardware & Software</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Ganti LCD & Baterai</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Perbaikan Mati Total</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Jual Beli HP Bekas</li>
                            <li><i class="bi bi-check-circle-fill text-success me-2"></i>Penjualan Sparepart</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="modern-card">
                        <h2 class="fw-bold mb-4">Lokasi & Jam Operasional</h2>
                        <div class="mb-4">
                            <h5 class="fw-bold">Alamat</h5>
                            <p class="text-muted"><i class="bi bi-geo-alt-fill me-2"></i><?= htmlspecialchars($settings['alamat'] ?? 'Jl. Contoh No. 123, Kota Anda') ?></p>
                        </div>
                        <div>
                            <h5 class="fw-bold">Jam Buka</h5>
                            <ul class="list-unstyled text-muted">
                                <li><i class="bi bi-clock-fill me-2"></i>Senin - Jumat: 09:00 - 18:00</li>
                                <li><i class="bi bi-clock-fill me-2"></i>Sabtu: 09:00 - 17:00</li>
                                <li><i class="bi bi-clock-history me-2"></i>Minggu: Tutup</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613507864!3d-6.194741395493371!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e67356080477!2sPT%20Traveloka%20Indonesia!5e0!3m2!1sid!2sid!4v1651951839658!5m2!1sid!2sid" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-muted">Jawaban untuk pertanyaan umum seputar layanan kami.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Berapa lama waktu servis HP?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Waktu servis tergantung jenis kerusakan. Perbaikan sederhana seperti ganti baterai atau LCD biasanya 1-2 jam. Kerusakan kompleks bisa 1-3 hari kerja.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Apakah sparepart yang digunakan original?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, kami hanya menggunakan sparepart original berkualitas tinggi. Semua produk dilengkapi dengan garansi resmi dari supplier.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Bagaimana sistem garansi?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Kami memberikan garansi 30 hari untuk pekerjaan servis dan garansi sesuai ketentuan supplier untuk sparepart yang diganti.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: var(--primary);">
        <div class="container text-center text-white">
            <h2 class="fw-bold mb-4">Siap Memperbaiki HP Anda?</h2>
            <p class="lead mb-5">Jangan tunggu kerusakan menjadi lebih parah. Hubungi kami sekarang!</p>
            <a href="booking.php" class="btn-modern">
                <i class="bi bi-calendar-plus me-2"></i>Booking Servis Sekarang
            </a>
        </div>
    </section>

<?php include 'views/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>