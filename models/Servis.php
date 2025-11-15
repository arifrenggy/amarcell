<?php
require_once __DIR__ . '/../config/database.php';

class Servis {
    private $conn;
    private $table_name = "servis";

    public $id;
    public $pelanggan_id;
    public $jenis_hp;
    public $kerusakan;
    public $estimasi_biaya;
    public $status;
    public $tanggal_masuk;
    public $tanggal_selesai;
    public $keterangan;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (pelanggan_id, jenis_hp, kerusakan, estimasi_biaya, status) VALUES (:pelanggan_id, :jenis_hp, :kerusakan, :estimasi_biaya, :status)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pelanggan_id', $data['pelanggan_id']);
        $stmt->bindParam(':jenis_hp', $data['jenis_hp']);
        $stmt->bindParam(':kerusakan', $data['kerusakan']);
        $stmt->bindParam(':estimasi_biaya', $data['estimasi_biaya']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll($status_filter = '', $limit = 10, $offset = 0, $search = '') {
        $query = "SELECT s.*, p.nama as nama_pelanggan, p.kontak 
                  FROM " . $this->table_name . " s 
                  LEFT JOIN pelanggan p ON s.pelanggan_id = p.id";
        
        $conditions = [];
        $params = [];

        if (!empty($status_filter)) {
            $conditions[] = "s.status = :status";
            $params[':status'] = $status_filter;
        }

        if (!empty($search)) {
            $conditions[] = "(s.jenis_hp LIKE :search OR s.kerusakan LIKE :search OR p.nama LIKE :search)";
            $params[':search'] = "%" . $search . "%";
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        $query .= " ORDER BY s.tanggal_masuk DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT s.*, p.nama as nama_pelanggan, p.kontak, p.email, p.alamat FROM " . $this->table_name . " s LEFT JOIN pelanggan p ON s.pelanggan_id = p.id WHERE s.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET jenis_hp = :jenis_hp, kerusakan = :kerusakan, estimasi_biaya = :estimasi_biaya, status = :status, keterangan = :keterangan";
        
        if (isset($data['tanggal_selesai'])) {
            $query .= ", tanggal_selesai = :tanggal_selesai";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':jenis_hp', $data['jenis_hp']);
        $stmt->bindParam(':kerusakan', $data['kerusakan']);
        $stmt->bindParam(':estimasi_biaya', $data['estimasi_biaya']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':keterangan', $data['keterangan']);
        $stmt->bindParam(':id', $id);
        
        if (isset($data['tanggal_selesai'])) {
            $stmt->bindParam(':tanggal_selesai', $data['tanggal_selesai']);
        }
        
        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status";
        
        if ($status == 'selesai') {
            $query .= ", tanggal_selesai = NOW()";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getByStatus($status) {
        $query = "SELECT s.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " s LEFT JOIN pelanggan p ON s.pelanggan_id = p.id WHERE s.status = :status ORDER BY s.tanggal_masuk DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCountByStatus() {
        $query = "SELECT status, COUNT(*) as jumlah FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($status_filter = null, $search = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s LEFT JOIN pelanggan p ON s.pelanggan_id = p.id";
        $conditions = [];
        $params = [];

        if ($status_filter !== null && $status_filter !== '') {
            $conditions[] = "s.status = :status_filter";
            $params[':status_filter'] = $status_filter;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = "(s.jenis_hp LIKE :search OR s.kerusakan LIKE :search OR p.nama LIKE :search)";
            $params[':search'] = "%" . $search . "%";
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }


    public function getRevenueByDateRange($start_date, $end_date) {
        $query = "SELECT DATE(tanggal_selesai) as tanggal, SUM(estimasi_biaya) as total_revenue FROM " . $this->table_name . " WHERE status = 'selesai' AND tanggal_selesai BETWEEN :start_date AND :end_date GROUP BY DATE(tanggal_selesai) ORDER BY tanggal";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil semua data servis berdasarkan rentang tanggal masuk.
     */
    public function getByDateRange($start_date, $end_date) {
        $query = "SELECT s.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " s 
                  LEFT JOIN pelanggan p ON s.pelanggan_id = p.id 
                  WHERE DATE(s.tanggal_masuk) BETWEEN :start_date AND :end_date 
                  ORDER BY s.tanggal_masuk DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
// Tambahkan closing tag PHP jika belum ada
?>
