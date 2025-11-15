<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Pelanggan.php';
require_once __DIR__ . '/models/Servis.php';
require_once __DIR__ . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();

$pelanggan = new Pelanggan($db);
$servis = new Servis($db);
$pengaturan = new Pengaturan($db);

$settings = $pengaturan->getSettings();

$success = false;
$error = '';

// Ambil data dari analisis jika ada
$jenis_hp_prefill = $_GET['jenis_hp'] ?? '';
$kerusakan_prefill = $_GET['kerusakan'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $kontak = sanitizeInput($_POST['kontak'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $jenis_hp = sanitizeInput($_POST['jenis_hp'] ?? '');
    $kerusakan = sanitizeInput($_POST['kerusakan'] ?? '');
    $alamat = sanitizeInput($_POST['alamat'] ?? '');
    $tanggal_booking = sanitizeInput($_POST['tanggal_booking'] ?? '');
    $jam_booking = sanitizeInput($_POST['jam_booking'] ?? '');

    if (empty($nama) || empty($kontak) || empty($jenis_hp) || empty($kerusakan)) {
        $error = 'Semua field yang bertanda * harus diisi';
    } elseif (!preg_match('/^[0-9]{10,13}$/', $kontak)) {
        $error = 'Nomor kontak tidak valid';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email tidak valid';
    } else {
        try {
            $pelanggan_data = $pelanggan->getByKontak($kontak);
            if (!$pelanggan_data) {
                $pelanggan_id = $pelanggan->create([
                    'nama' => $nama,
                    'kontak' => $kontak,
                    'email' => $email,
                    'alamat' => $alamat
                ]);
            } else {
                $pelanggan_id = $pelanggan_data['id'];
                $pelanggan->update($pelanggan_id, [
                    'nama' => $nama,
                    'kontak' => $kontak,
                    'email' => $email,
                    'alamat' => $alamat
                ]);
            }

            $estimasi_biaya = hitungEstimasiBiaya($jenis_hp, $kerusakan);

            $servis_id = $servis->create([
                'pelanggan_id' => $pelanggan_id,
                'jenis_hp' => $jenis_hp,
                'kerusakan' => $kerusakan,
                'estimasi_biaya' => $estimasi_biaya,
                'status' => 'menunggu'
            ]);

            if ($servis_id) {
                $success = true;

                if (!empty($settings['nomor_whatsapp'])) {
                    kirimNotifikasiWhatsApp($kontak, $nama, $jenis_hp, $servis_id);
                }
            } else {
                $error = 'Gagal menyimpan data booking. Silakan coba lagi.';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

function hitungEstimasiBiaya($jenis_hp, $kerusakan) {
    $estimasi_db = [
        'iphone' => [
            'lcd_retak' => 350000,
            'baterai_cepat_habis' => 150000,
            'charging_tidak_masuk' => 200000,
            'tombol_tidak_berfungsi' => 180000,
            'kamera_gelap' => 250000,
            'speaker_tidak_bunyi' => 120000,
            'mic_tidak_bisa_dengar' => 100000,
            'wifi_tidak_terhubung' => 300000
        ],
        'samsung' => [
            'lcd_retak' => 280000,
            'baterai_cepat_habis' => 120000,
            'charging_tidak_masuk' => 150000,
            'tombol_tidak_berfungsi' => 130000,
            'kamera_gelap' => 200000,
            'speaker_tidak_bunyi' => 100000,
            'mic_tidak_bisa_dengar' => 80000
        ],
        'xiaomi' => [
            'lcd_retak' => 200000,
            'baterai_cepat_habis' => 100000,
            'charging_tidak_masuk' => 120000
        ],
        'vivo' => [
            'lcd_retak' => 220000,
            'baterai_cepat_habis' => 90000
        ],
        'oppo' => [
            'lcd_retak' => 210000,
            'baterai_cepat_habis' => 95000
        ]
    ];

    return $estimasi_db[strtolower($jenis_hp)][strtolower(str_replace(' ', '_', $kerusakan))] ?? 150000;
}

function kirimNotifikasiWhatsApp($kontak, $nama, $jenis_hp, $servis_id) {
    // Placeholder untuk integrasi WhatsApp
    return true;
}

$title = $settings['nama_toko'] ?? 'Amar Cell Service';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Servis HP - <?= htmlspecialchars($title) ?></title>
  <meta name="description" content="Booking servis HP online dengan teknisi profesional. Servis cepat, harga transparan, dan garansi pekerjaan.">
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

    .success-card-modern {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #fff;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
      text-align: center;
    }

    .step-indicator-modern {
      width: 50px;
      height: 50px;
      background: var(--accent);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      margin-bottom: 1rem;
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
          <i class="bi bi-calendar-plus me-3"></i>Booking Servis HP
        </h1>
        <p class="hero-subtitle-modern">
          Pesan servis HP Anda secara online dengan teknisi profesional. Cepat, transparan, dan bergaransi.
        </p>
        <div class="d-flex gap-2 flex-wrap">
          <span class="badge bg-light text-dark">Cepat</span>
          <span class="badge bg-light text-dark">Profesional</span>
          <span class="badge bg-light text-dark">Bergaransi</span>
        </div>
      </div>
      <div class="col-lg-5 text-center d-none d-lg-block">
        <i class="bi bi-calendar-check display-1 text-warning opacity-75"></i>
      </div>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <?php if ($success): ?>
      <div class="success-card-modern animate-fadeInUp">
        <i class="bi bi-check-circle-fill display-4 mb-3"></i>
        <h3 class="fw-bold mb-3">Booking Berhasil!</h3>
        <p class="lead mb-4">
          Terima kasih telah membooking servis di <?= htmlspecialchars($title) ?>. Kami akan segera menghubungi Anda untuk konfirmasi.
        </p>
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <div class="bg-white bg-opacity-20 rounded p-3">
              <h6>Nomor Booking</h6>
              <h4 class="fw-bold">#SRV<?= str_pad($servis_id, 4, '0', STR_PAD_LEFT) ?></h4>
            </div>
          </div>
          <div class="col-md-6">
            <div class="bg-white bg-opacity-20 rounded p-3">
              <h6>Estimasi Biaya</h6>
              <h4 class="fw-bold">Rp <?= number_format($estimasi_biaya, 0, ',', '.') ?></h4>
            </div>
          </div>
        </div>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="index.php" class="btn btn-light">
            <i class="bi bi-house me-2"></i>Kembali ke Beranda
          </a>
          <a href="https://wa.me/<?= $settings['nomor_whatsapp'] ?? '62' ?>" class="btn btn-outline-light">
            <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
          </a>
        </div>
      </div>
    <?php else: ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="modern-card">
            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <!-- Step 1: Data Pelanggan -->
              <div class="mb-5">
                <div class="d-flex align-items-center mb-4">
                  <div class="step-indicator-modern me-3">1</div>
                  <div>
                    <h4 class="fw-bold mb-0">Data Pelanggan</h4>
                    <p class="text-muted mb-0">Informasi kontak Anda</p>
                  </div>
                </div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control-modern" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control-modern" name="kontak" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['kontak'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email (Opsional)</label>
                    <input type="email" class="form-control-modern" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Alamat (Opsional)</label>
                    <input type="text" class="form-control-modern" name="alamat" value="<?= htmlspecialchars($_POST['alamat'] ?? '') ?>">
                  </div>
                </div>
              </div>

              <!-- Step 2: Data HP -->
              <div class="mb-5">
                <div class="d-flex align-items-center mb-4">
                  <div class="step-indicator-modern me-3">2</div>
                  <div>
                    <h4 class="fw-bold mb-0">Data HP</h4>
                    <p class="text-muted mb-0">Informasi HP yang akan diservis</p>
                  </div>
                </div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Jenis HP <span class="text-danger">*</span></label>
                    <select class="form-control-modern" name="jenis_hp" required>
                      <option value="">-- Pilih Jenis HP --</option>
                      <option value="iPhone" <?= $jenis_hp_prefill == 'iphone' || ($_POST['jenis_hp'] ?? '') == 'iPhone' ? 'selected' : '' ?>>iPhone</option>
                      <option value="Samsung" <?= $jenis_hp_prefill == 'samsung' || ($_POST['jenis_hp'] ?? '') == 'Samsung' ? 'selected' : '' ?>>Samsung</option>
                      <option value="Xiaomi" <?= $jenis_hp_prefill == 'xiaomi' || ($_POST['jenis_hp'] ?? '') == 'Xiaomi' ? 'selected' : '' ?>>Xiaomi</option>
                      <option value="Vivo" <?= $jenis_hp_prefill == 'vivo' || ($_POST['jenis_hp'] ?? '') == 'Vivo' ? 'selected' : '' ?>>Vivo</option>
                      <option value="Oppo" <?= $jenis_hp_prefill == 'oppo' || ($_POST['jenis_hp'] ?? '') == 'Oppo' ? 'selected' : '' ?>>Oppo</option>
                      <option value="Lainnya" <?= ($_POST['jenis_hp'] ?? '') == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Model HP (Opsional)</label>
                    <input type="text" class="form-control-modern" name="model_hp" placeholder="Contoh: iPhone 12 Pro Max" value="<?= htmlspecialchars($_POST['model_hp'] ?? '') ?>">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold">Deskripsi Kerusakan <span class="text-danger">*</span></label>
                    <textarea class="form-control-modern" name="kerusakan" rows="3" placeholder="Jelaskan secara detail kerusakan yang dialami HP Anda..." required><?= htmlspecialchars($_POST['kerusakan'] ?? ($kerusakan_prefill ? 'Kerusakan: ' . str_replace('_', ' ', $kerusakan_prefill) : '')) ?></textarea>
                  </div>
                </div>
              </div>

              <!-- Step 3: Jadwal Servis -->
              <div class="mb-5">
                <div class="d-flex align-items-center mb-4">
                  <div class="step-indicator-modern me-3">3</div>
                  <div>
                    <h4 class="fw-bold mb-0">Jadwal Servis</h4>
                    <p class="text-muted mb-0">Pilih waktu yang sesuai untuk Anda</p>
                  </div>
                </div>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Servis <span class="text-danger">*</span></label>
                    <input type="date" class="form-control-modern" name="tanggal_booking" value="<?= htmlspecialchars($_POST['tanggal_booking'] ?? '') ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Jam Servis <span class="text-danger">*</span></label>
                    <select class="form-control-modern" name="jam_booking" required>
                      <option value="">-- Pilih Jam --</option>
                      <option value="09:00" <?= ($_POST['jam_booking'] ?? '') == '09:00' ? 'selected' : '' ?>>09:00 - 10:00</option>
                      <option value="10:00" <?= ($_POST['jam_booking'] ?? '') == '10:00' ? 'selected' : '' ?>>10:00 - 11:00</option>
                      <option value="11:00" <?= ($_POST['jam_booking'] ?? '') == '11:00' ? 'selected' : '' ?>>11:00 - 12:00</option>
                      <option value="13:00" <?= ($_POST['jam_booking'] ?? '') == '13:00' ? 'selected' : '' ?>>13:00 - 14:00</option>
                      <option value="14:00" <?= ($_POST['jam_booking'] ?? '') == '14:00' ? 'selected' : '' ?>>14:00 - 15:00</option>
                      <option value="15:00" <?= ($_POST['jam_booking'] ?? '') == '15:00' ? 'selected' : '' ?>>15:00 - 16:00</option>
                      <option value="16:00" <?= ($_POST['jam_booking'] ?? '') == '16:00' ? 'selected' : '' ?>>16:00 - 17:00</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Terms & Submit -->
              <div class="mb-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                  <label class="form-check-label" for="terms">
                    Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> servis
                  </label>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn-modern btn-lg">
                  <i class="bi bi-calendar-check me-2"></i>Booking Sekarang
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Info Card -->
        <div class="col-lg-4 mt-4 mt-lg-0">
          <div class="modern-card">
            <h5 class="fw-bold mb-3">
              <i class="bi bi-info-circle text-primary me-2"></i>
              Informasi Penting
            </h5>
            <ul class="list-unstyled">
              <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Booking gratis tanpa biaya tambahan</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Konfirmasi via WhatsApp dalam 15 menit</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Garansi pekerjaan 30 hari</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Estimasi biaya transparan</li>
              <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Teknisi berpengalaman & profesional</li>
            </ul>
            <hr>
            <h6 class="fw-bold">Jam Operasional</h6>
            <p class="text-muted mb-1">Senin - Sabtu: 09:00 - 18:00</p>
            <p class="text-muted">Minggu: Tutup</p>
            <a href="https://wa.me/<?= $settings['nomor_whatsapp'] ?? '62' ?>" class="btn btn-success btn-sm w-100 mt-2">
              <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Modal Syarat & Ketentuan -->
<div class="modal fade" id="termsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Syarat dan Ketentuan Servis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h6>1. Booking Servis</h6>
        <ul>
          <li>Booking servis gratis tanpa biaya tambahan</li>
          <li>Konfirmasi booking akan dikirim via WhatsApp dalam 15 menit</li>
          <li>Customer dapat membatalkan booking maksimal 2 jam sebelum jadwal</li>
        </ul>
        <h6>2. Biaya Servis</h6>
        <ul>
          <li>Biaya yang tertera adalah estimasi awal</li>
          <li>Biaya final akan dikonfirmasi setelah pengecekan menyeluruh</li>
          <li>Pembayaran dapat dilakukan dengan tunai, transfer, atau QRIS</li>
        </ul>
        <h6>3. Garansi</h6>
        <ul>
          <li>Garansi pekerjaan selama 30 hari</li>
          <li>Garansi tidak berlaku untuk kerusakan akibat kelalaian user</li>
          <li>Garansi sparepart sesuai dengan ketentuan supplier</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>

<?php include 'views/footer.php'; ?>

  <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Set minimum date to today
  const today = new Date().toISOString().split('T')[0];
  const dateInput = document.querySelector('input[name="tanggal_booking"]');
  if (dateInput) {
    dateInput.min = today;
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    dateInput.value = tomorrow.toISOString().split('T')[0];
  }

  // Auto-hide alert
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }
  }, 5000);
</script>
</body>
</html><?php
?>