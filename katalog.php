<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Barang.php';
require_once __DIR__ . '/models/Pengaturan.php';

$database = new Database();
$db = $database->getConnection();

$barang = new Barang($db);
$pengaturan = new Pengaturan($db);

$settings = $pengaturan->getSettings();

// Fetch products for each category, sorted by popularity
$spareparts = $barang->getAllByCategorySortedByPopularity('sparepart');
$hp_bekas = $barang->getAllByCategorySortedByPopularity('hp_bekas');

$title = $settings['nama_toko'] ?? 'Amar Cell Service';

// Helper function to render a product card
function renderProductCard($product) {
    $foto_url = 'assets/img/placeholder-product.jpg'; // Default placeholder
    if (!empty($product['foto'])) {
        if (filter_var($product['foto'], FILTER_VALIDATE_URL)) {
            $foto_url = htmlspecialchars($product['foto']);
        } else {
            $foto_url = htmlspecialchars($product['foto']);
        }
    }

    $stok_badge = '';
    if ($product['stok'] > 10) {
        $stok_badge = '<span class="badge bg-success-subtle text-success-emphasis rounded-pill">Stok Banyak</span>';
    } elseif ($product['stok'] > 0) {
        $stok_badge = '<span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Stok Terbatas</span>';
    } else {
        $stok_badge = '<span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Stok Habis</span>';
    }

    return '
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="modern-card p-3">
                <div class="product-image">
                    <img src="' . $foto_url . '" alt="' . htmlspecialchars($product['nama_barang']) . '">
                </div>
                <div class="card-body p-0">
                    <h5 class="fw-bold mb-2" style="font-size: 1rem;">' . htmlspecialchars($product['nama_barang']) . '</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="price-tag" style="font-size: 1.25rem;">Rp ' . number_format($product['harga_jual'], 0, ',', '.') . '</div>
                        ' . $stok_badge . '
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn-modern" onclick="buyProduct(' . $product['id'] . ')" ' . ($product['stok'] <= 0 ? 'disabled' : '') . '>
                            <i class="bi bi-cart-plus me-2"></i>
                            Beli
                        </button>
                    </div>
                </div>
            </div>
        </div>
    ';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - <?= htmlspecialchars($title) ?></title>
    <meta name="description" content="Katalog lengkap sparepart HP dan HP bekas berkualitas dengan harga terjangkau dan garansi resmi.">
    
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
    
    .product-image {
        height: 200px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .price-tag {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
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
    </style>
</head>
<body>
<?php include 'views/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section-modern">
        <div class="container text-center">
            <h1 class="hero-title-modern">
                <i class="bi bi-collection me-3"></i>Katalog Produk
            </h1>
            <p class="hero-subtitle-modern">
                Temukan sparepart berkualitas dan HP bekas terpercaya dengan harga terjangkau.
            </p>
        </div>
    </section>

    <!-- Spareparts Section -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-boxes fs-2 text-primary me-3"></i>
                <h2 class="fw-bold mb-0">Sparepart</h2>
            </div>
            <?php if (empty($spareparts)): ?>
                <div class="text-center py-5 modern-card">
                    <p class="text-muted mb-0">Tidak ada sparepart yang tersedia saat ini.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($spareparts as $product): ?>
                        <?= renderProductCard($product) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- HP Bekas Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-phone fs-2 text-primary me-3"></i>
                <h2 class="fw-bold mb-0">HP Bekas</h2>
            </div>
            <?php if (empty($hp_bekas)): ?>
                <div class="text-center py-5 modern-card">
                    <p class="text-muted mb-0">Tidak ada HP bekas yang tersedia saat ini.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($hp_bekas as $product): ?>
                        <?= renderProductCard($product) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'views/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function buyProduct(productId) {
        alert('Fitur pembelian akan segera hadir. Silakan hubungi kami via WhatsApp untuk pembelian.');
    }
</script>
</body>
</html>
