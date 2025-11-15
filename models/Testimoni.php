<?php
require_once __DIR__ . '/../config/database.php';

class Testimoni {
    private $conn;
    private $table_name = "testimoni";

    public $id;
    public $pelanggan_id;
    public $isi;
    public $rating;
    public $tanggal;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (pelanggan_id, isi, rating, status) VALUES (:pelanggan_id, :isi, :rating, :status)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pelanggan_id', $data['pelanggan_id']);
        $stmt->bindParam(':isi', $data['isi']);
        $stmt->bindParam(':rating', $data['rating']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll($status = 'diterima', $limit = null, $offset = null) {
        $query = "SELECT t.*, p.nama as nama_pelanggan FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id";
        
        if ($status !== null) {
            $query .= " WHERE t.status = :status";
        }
        
        $query .= " ORDER BY t.tanggal DESC";
        
        if ($limit !== null) {
            $query .= " LIMIT " . $limit;
            if ($offset !== null) {
                $query .= " OFFSET " . $offset;
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($status !== null) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT t.*, p.nama as nama_pelanggan, p.kontak FROM " . $this->table_name . " t LEFT JOIN pelanggan p ON t.pelanggan_id = p.id WHERE t.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        
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

    public function getAverageRating() {
        $query = "SELECT AVG(rating) as avg_rating FROM " . $this->table_name . " WHERE status = 'diterima'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0;
    }

    public function getRatingDistribution() {
        $query = "SELECT rating, COUNT(*) as jumlah FROM " . $this->table_name . " WHERE status = 'diterima' GROUP BY rating ORDER BY rating DESC";
        $stmt = $this->conn->prepare($query);
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

    public function getPendingCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>