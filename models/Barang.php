<?php
require_once __DIR__ . '/../config/database.php';

class Barang {
    private $conn;
    private $table_name = "barang";

    public $id;
    public $nama_barang;
    public $kategori;
    public $stok;
    public $harga_modal;
    public $harga_jual;
    public $deskripsi;
    public $foto;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nama_barang, kategori, stok, harga_modal, harga_jual, deskripsi, foto) VALUES (:nama_barang, :kategori, :stok, :harga_modal, :harga_jual, :deskripsi, :foto)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_barang', $data['nama_barang']);
        $stmt->bindParam(':kategori', $data['kategori']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':harga_modal', $data['harga_modal']);
        $stmt->bindParam(':harga_jual', $data['harga_jual']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':foto', $data['foto']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAll($kategori = '', $limit = 10, $offset = 0, $search = '') {
        $query = "SELECT * FROM " . $this->table_name;
        $conditions = [];
        $params = [];

        if (!empty($kategori)) {
            $conditions[] = "kategori = :kategori";
            $params[':kategori'] = $kategori;
        }

        if (!empty($search)) {
            $conditions[] = "nama_barang LIKE :search";
            $params[':search'] = "%" . $search . "%";
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " ORDER BY nama_barang LIMIT :limit OFFSET :offset";

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
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nama_barang = :nama_barang, kategori = :kategori, stok = :stok, harga_modal = :harga_modal, harga_jual = :harga_jual, deskripsi = :deskripsi";
        
        if (isset($data['foto'])) {
            $query .= ", foto = :foto";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_barang', $data['nama_barang']);
        $stmt->bindParam(':kategori', $data['kategori']);
        $stmt->bindParam(':stok', $data['stok']);
        $stmt->bindParam(':harga_modal', $data['harga_modal']);
        $stmt->bindParam(':harga_jual', $data['harga_jual']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':id', $id);
        
        if (isset($data['foto'])) {
            $stmt->bindParam(':foto', $data['foto']);
        }
        
        return $stmt->execute();
    }

    public function updateStok($id, $stok) {
        $query = "UPDATE " . $this->table_name . " SET stok = stok + :stok WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stok', $stok);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function search($keyword, $kategori = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE nama_barang LIKE :keyword";
        
        if ($kategori !== null) {
            $query .= " AND kategori = :kategori";
        }
        
        $query .= " ORDER BY nama_barang";
        
        $search = "%" . $keyword . "%";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':keyword', $search);
        
        if ($kategori !== null) {
            $stmt->bindParam(':kategori', $kategori);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStokMenipis() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE stok <= 5 ORDER BY stok ASC, nama_barang";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($kategori = null, $search = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $conditions = [];
        $params = [];

        if ($kategori !== null) {
            $conditions[] = "kategori = :kategori";
            $params[':kategori'] = $kategori;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = "nama_barang LIKE :search";
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

    public function getStatsByCategory() {
        $query = "SELECT kategori, COUNT(*) as jumlah_barang, SUM(stok) as total_stok FROM " . $this->table_name . " GROUP BY kategori";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNewest($limit = 8) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopular($limit = 8) {
        $query = "SELECT b.*, SUM(dt.jumlah) as total_terjual
                  FROM " . $this->table_name . " b
                  JOIN detail_transaksi dt ON b.id = dt.id_barang
                  GROUP BY b.id
                  ORDER BY total_terjual DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByCategorySortedByPopularity($kategori) {
        $query = "SELECT b.*, SUM(dt.jumlah) as total_terjual
                  FROM " . $this->table_name . " b
                  LEFT JOIN detail_transaksi dt ON b.id = dt.id_barang
                  WHERE b.kategori = :kategori
                  GROUP BY b.id
                  ORDER BY total_terjual DESC, b.nama_barang ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>