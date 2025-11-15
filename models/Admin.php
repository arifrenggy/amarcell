<?php
require_once __DIR__ . '/../config/database.php';

class Admin {
    private $conn;
    private $table_name = "admin";

    public $id;
    public $username;
    public $password;
    public $email;
    public $nama_lengkap;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password, email, nama_lengkap FROM " . $this->table_name . " WHERE username = :username OR email = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->nama_lengkap = $row['nama_lengkap'];
            return true;
        }
        
        return false;
    }

    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nama_lengkap = :nama_lengkap, email = :email";
        
        if (isset($data['password']) && !empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $id);
        
        if (isset($data['password']) && !empty($data['password'])) {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }
        
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT id, username, email, nama_lengkap, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>