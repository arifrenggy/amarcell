<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Barang.php';

checkLogin();

$database = new Database();
$db = $database->getConnection();

$transaksi = new Transaksi($db);
$pelanggan = new Pelanggan($db);
$barang = new Barang($db);
$detailTransaksi = new DetailTransaksi($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pelanggan_id = $_POST['pelanggan_id'] ?? '';
    $items = $_POST['items'] ?? [];
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? 'tunai';
    $keterangan = $_POST['keterangan'] ?? 'Penjualan';

    if (empty($pelanggan_id) || empty($items)) {
        $error = 'Pelanggan dan minimal satu barang harus dipilih.';
    } else {
        $total_transaksi = 0;
        $db->beginTransaction();
        try {
            foreach ($items as $item) {
                $barang_data = $barang->getById($item['id']);
                if (!$barang_data || $barang_data['stok'] < $item['jumlah']) {
                    throw new Exception('Stok untuk barang "' . ($barang_data['nama_barang'] ?? 'Unknown') . '" tidak mencukupi.');
                }
                $total_transaksi += $item['jumlah'] * $barang_data['harga_jual'];
            }

            $transaksi_data = [
                'pelanggan_id' => $pelanggan_id,
                'total' => $total_transaksi,
                'metode_pembayaran' => $metode_pembayaran,
                'status' => 'selesai',
                'keterangan' => $keterangan
            ];
            $transaksi_id = $transaksi->create($transaksi_data);

            if (!$transaksi_id) {
                throw new Exception('Gagal membuat data transaksi utama.');
            }

            foreach ($items as $item) {
                $barang_data = $barang->getById($item['id']);
                $detail_data = [
                    'id_transaksi' => $transaksi_id,
                    'id_barang' => $item['id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $barang_data['harga_jual'],
                    'subtotal' => $item['jumlah'] * $barang_data['harga_jual']
                ];
                
                if (!$detailTransaksi->create($detail_data)) {
                    throw new Exception('Gagal menyimpan detail untuk barang "' . $barang_data['nama_barang'] . '".');
                }
                
                if (!$barang->updateStok($item['id'], -$item['jumlah'])) {
                     throw new Exception('Gagal mengupdate stok untuk barang "' . $barang_data['nama_barang'] . '".');
                }
            }

            $db->commit();
            $_SESSION['success'] = 'Transaksi berhasil dibuat dengan ID #' . $transaksi_id;
            header('Location: transaksi.php');
            exit();

        } catch (Exception $e) {
            $db->rollBack();
            $error = $e->getMessage();
        }
    }
}

$list_pelanggan = $pelanggan->getAll();
$list_barang = $barang->getAll();
$current_page = 'transaksi';
$page_title = 'Tambah Transaksi Baru';

include 'views/template.php';
?>

<div class="container-fluid p-4">
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" id="transactionForm">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">Keranjang Belanja</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th style="width: 120px;">Jumlah</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td colspan="2" class="fw-bold fs-5" id="cart-total">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="cart-empty-message" class="text-center py-4 text-muted">
                            <i class="bi bi-cart-x fs-1"></i>
                            <p class="mt-2">Keranjang masih kosong</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">Detail Transaksi</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <select name="pelanggan_id" class="form-select" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($list_pelanggan as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Tambah Barang</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Barang</label>
                            <select id="product-select" class="form-select">
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($list_barang as $b): ?>
                                    <option value="<?= $b['id'] ?>" data-nama="<?= htmlspecialchars($b['nama_barang']) ?>" data-harga="<?= $b['harga_jual'] ?>" data-stok="<?= $b['stok'] ?>">
                                        <?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="button" id="add-to-cart" class="btn btn-outline-primary">Tambah ke Keranjang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalEl = document.getElementById('cart-total');
    const productSelect = document.getElementById('product-select');
    const addToCartBtn = document.getElementById('add-to-cart');
    const cartEmptyMessage = document.getElementById('cart-empty-message');
    let cart = [];

    addToCartBtn.addEventListener('click', () => {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (!selectedOption.value) return;

        const item = {
            id: selectedOption.value,
            nama: selectedOption.dataset.nama,
            harga: parseFloat(selectedOption.dataset.harga),
            stok: parseInt(selectedOption.dataset.stok),
            jumlah: 1
        };

        const existingItem = cart.find(i => i.id === item.id);
        if (existingItem) {
            if (existingItem.jumlah < existingItem.stok) existingItem.jumlah++;
            else alert('Stok tidak mencukupi!');
        } else {
            cart.push(item);
        }
        renderCart();
    });

    function renderCart() {
        cartItemsContainer.innerHTML = '';
        let total = 0;
        cartEmptyMessage.style.display = cart.length === 0 ? 'block' : 'none';

        cart.forEach((item, index) => {
            const subtotal = item.jumlah * item.harga;
            total += subtotal;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.nama}<input type="hidden" name="items[${index}][id]" value="${item.id}"></td>
                <td><input type="number" name="items[${index}][jumlah]" class="form-control form-control-sm quantity-input" value="${item.jumlah}" min="1" max="${item.stok}" data-index="${index}"></td>
                <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td><button type="button" class="btn btn-danger btn-sm remove-item" data-index="${index}"><i class="bi bi-trash"></i></button></td>
            `;
            cartItemsContainer.appendChild(row);
        });
        cartTotalEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        attachEventListeners();
    }

    function attachEventListeners() {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', (e) => {
                const index = e.target.dataset.index;
                let newJumlah = parseInt(e.target.value);
                if (newJumlah > cart[index].stok) {
                    alert('Stok tidak mencukupi!');
                    newJumlah = cart[index].stok;
                    e.target.value = newJumlah;
                }
                cart[index].jumlah = newJumlah;
                renderCart();
            });
        });
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', (e) => {
                cart.splice(e.currentTarget.dataset.index, 1);
                renderCart();
            });
        });
    }
    renderCart();
});
</script>

<?php include 'views/footer.php'; ?>
