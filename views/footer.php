<footer class="main-footer-modern">
  <div class="container">
    <div class="row gy-4">
      <!-- Brand & Kontak -->
      <div class="col-lg-4">
        <h5 class="footer-brand">
          <i class="bi bi-tools me-2"></i><?= htmlspecialchars($title) ?>
        </h5>
        <p class="footer-desc">
          <?= htmlspecialchars($settings['alamat'] ?? 'Alamat toko belum diatur.') ?>
        </p>
        <ul class="footer-contact">
          <li>
            <i class="bi bi-whatsapp text-success"></i>
            <a href="<?= $whatsapp_link ?>" target="_blank">
              <?= htmlspecialchars($settings['nomor_whatsapp'] ?? '081-XXX-XXXX') ?>
            </a>
          </li>
          <li>
            <i class="bi bi-envelope text-danger"></i>
            <a href="mailto:<?= htmlspecialchars($settings['email_bisnis'] ?? 'email@bisnis.com') ?>">
              <?= htmlspecialchars($settings['email_bisnis'] ?? 'email@bisnis.com') ?>
            </a>
          </li>
        </ul>
      </div>

      <!-- Layanan -->
      <div class="col-lg-2 col-6">
        <h6 class="footer-heading">Layanan</h6>
        <ul class="footer-links">
          <li><a href="analisis.php">Analisis Kerusakan</a></li>
          <li><a href="booking.php">Booking Servis</a></li>
          <li><a href="katalog.php?kategori=sparepart">Sparepart</a></li>
          <li><a href="katalog.php?kategori=hp_bekas">HP Bekas</a></li>
        </ul>
      </div>

      <!-- Perusahaan -->
      <div class="col-lg-2 col-6">
        <h6 class="footer-heading">Perusahaan</h6>
        <ul class="footer-links">
          <li><a href="kontak.php#profil">Tentang Kami</a></li>
          <li><a href="kontak.php">Lokasi Toko</a></li>
          <li><a href="testimoni.php">Ulasan Pelanggan</a></li>
          <li><a href="admin/login.php">Admin Login</a></li>
        </ul>
      </div>

      <!-- Jam Operasional -->
      <div class="col-lg-4">
        <h6 class="footer-heading">Jam Operasional</h6>
        <p class="footer-desc">
          <?= nl2br(htmlspecialchars($settings['jam_buka'] ?? 'Senin - Sabtu: 09:00 - 18:00\nMinggu: Tutup')) ?>
        </p>
        <a href="booking.php" class="btn-footer-action">Pesan Sekarang</a>
      </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom">
      <div class="col-12 text-center">
        <p class="mb-0">
          &copy; <?= date('Y') ?> <strong><?= htmlspecialchars($title) ?></strong>.
          Dibuat dengan <i class="bi bi-heart-fill text-danger"></i> oleh Teknisi Profesional.
        </p>
      </div>
    </div>
  </div>
</footer>

<!-- Styling -->
<style>
.main-footer-modern {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  color: #e2e8f0;
  padding: 60px 0 20px;
  font-size: 0.95rem;
}

.footer-brand {
  font-size: 1.25rem;
  font-weight: 700;
  color: #f8fafc;
  margin-bottom: 1rem;
}

.footer-desc {
  color: #94a3b8;
  margin-bottom: 1rem;
}

.footer-contact {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-contact li {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.footer-contact i {
  margin-right: 0.5rem;
  font-size: 1rem;
}

.footer-contact a {
  color: #e2e8f0;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-contact a:hover {
  color: #d4af37;
}

.footer-heading {
  font-size: 1rem;
  font-weight: 600;
  color: #f8fafc;
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.5rem;
}

.footer-links a {
  color: #94a3b8;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-links a:hover {
  color: #d4af37;
}

.btn-footer-action {
  display: inline-block;
  background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
  color: #fff;
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

.btn-footer-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
  color: #fff;
}

.footer-bottom {
  margin-top: 3rem;
  padding-top: 1.5rem;
  border-top: 1px solid #334155;
  font-size: 0.85rem;
  color: #94a3b8;
}

@media (max-width: 768px) {
  .footer-heading {
    margin-top: 1.5rem;
  }
}
</style>