<?php
require_once __DIR__ . '/../config/database.php';

class Transaksi {
    private $conn;
    private $table_name = "transaksi";

    public $id;
    public $pelanggan_id;
    public $tanggal;
    public $total;
    public $metode_pembayaran;
    public $status;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (pelanggan_id, total, metode_pembayaran, status, keterangan) VALUES (:pelanggan_id, :total, :metode_pembayaran, :status, :keterangan)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pelanggan_id', $data['pelanggan_id']);
        $stmt->bindParam(':total', $data['total']);
        $stmt->bindParam(':metode_pembayaran', $data['metode_pembayaran']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':keterangan', $data['keterangan']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll($limit = null, $offset = null) {
        $query = "SELECT t.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id ORDER BY t.tanggal DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . $limit;
            if ($offset !== null) {
                $query .= " OFFSET " . $offset;
            }
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT t.*, p.nama as nama_pelanggan, p.kontak, p.email, p.alamat FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id WHERE t.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET total = :total, metode_pembayaran = :metode_pembayaran, status = :status, keterangan = :keterangan WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':total', $data['total']);
        $stmt->bindParam(':metode_pembayaran', $data['metode_pembayaran']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':keterangan', $data['keterangan']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        // Hapus detail transaksi terlebih dahulu
        $query = "DELETE FROM detail_transaksi WHERE id_transaksi = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Hapus transaksi
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getByDateRange($start_date, $end_date) {
        $query = "SELECT t.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id WHERE DATE(t.tanggal) BETWEEN :start_date AND :end_date ORDER BY t.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRevenue($start_date = null, $end_date = null) {
        $query = "SELECT SUM(total) as total_revenue FROM " . $this->table_name . " WHERE status = 'selesai'";
        
        if ($start_date !== null && $end_date !== null) {
            $query .= " AND DATE(tanggal) BETWEEN :start_date AND :end_date";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($start_date !== null && $end_date !== null) {
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    }

    public function getRevenueByDateRange($start_date, $end_date) {
        $query = "SELECT DATE(tanggal) as tanggal, SUM(total) as total_revenue FROM " . $this->table_name . " WHERE status = 'selesai' AND DATE(tanggal) BETWEEN :start_date AND :end_date GROUP BY DATE(tanggal) ORDER BY tanggal";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($status = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        if ($status !== null) {
            $query .= " WHERE status = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($status !== null) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTodayTransactions() {
        $query = "SELECT t.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id WHERE DATE(t.tanggal) = CURDATE() ORDER BY t.tanggal DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class DetailTransaksi {
    private $conn;
    private $table_name = "detail_transaksi";

    public $id;
    public $id_transaksi;
    public $id_barang;
    public $jumlah;
    public $harga_satuan;
    public $subtotal;
    public $jasa_pasang;
    public $biaya_pasang;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (id_transaksi, id_barang, jumlah, harga_satuan, subtotal, jasa_pasang, biaya_pasang) VALUES (:id_transaksi, :id_barang, :jumlah, :harga_satuan, :subtotal, :jasa_pasang, :biaya_pasang)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_transaksi', $data['id_transaksi']);
        $stmt->bindParam(':id_barang', $data['id_barang']);
        $stmt->bindParam(':jumlah', $data['jumlah']);
        $stmt->bindParam(':harga_satuan', $data['harga_satuan']);
        $stmt->bindParam(':subtotal', $data['subtotal']);
        $stmt->bindParam(':jasa_pasang', $data['jasa_pasang']);
        $stmt->bindParam(':biaya_pasang', $data['biaya_pasang']);
        
        return $stmt->execute();
    }

    public function getByTransaksiId($id_transaksi) {
        $query = "SELECT dt.*, b.nama_barang, b.kategori FROM " . $this->table_name . " dt JOIN barang b ON dt.id_barang = b.id WHERE dt.id_transaksi = :id_transaksi";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_transaksi', $id_transaksi);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByTransaksiId($id_transaksi) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_transaksi = :id_transaksi";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_transaksi', $id_transaksi);
        
        return $stmt->execute();
    }
}
?>