<?php
require_once __DIR__ . '/../config/database.php';

class Pelanggan {
    private $conn;
    private $table_name = "pelanggan";

    public $id;
    public $nama;
    public $kontak;
    public $email;
    public $alamat;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nama, kontak, email, alamat) VALUES (:nama, :kontak, :email, :alamat)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':kontak', $data['kontak']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':alamat', $data['alamat']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll($limit = null, $offset = null) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY nama";
        
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
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByKontak($kontak) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE kontak = :kontak LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kontak', $kontak);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nama = :nama, kontak = :kontak, email = :email, alamat = :alamat WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama', $data['nama']);
        $stmt->bindParam(':kontak', $data['kontak']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nama LIKE :keyword OR kontak LIKE :keyword OR email LIKE :keyword ORDER BY nama";
        
        $search = "%" . $keyword . "%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $search);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>