<?php
require_once __DIR__ . '/../config/database.php';

class Pengaturan {
    private $conn;
    private $table_name = "pengaturan";

    public $id;
    public $nama_toko;
    public $alamat;
    public $nomor_whatsapp;
    public $email_bisnis;
    public $jam_buka;
    public $logo;
    public $updated_at;
    public $gemini_api_key;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getSettings() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . " SET nama_toko = :nama_toko, alamat = :alamat, nomor_whatsapp = :nomor_whatsapp, email_bisnis = :email_bisnis, jam_buka = :jam_buka, gemini_api_key = :gemini_api_key";
        
        if (isset($data['logo'])) {
            $query .= ", logo = :logo";
        }
        
        $query .= " WHERE id = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_toko', $data['nama_toko']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':nomor_whatsapp', $data['nomor_whatsapp']);
        $stmt->bindParam(':email_bisnis', $data['email_bisnis']);
        $stmt->bindParam(':jam_buka', $data['jam_buka']);
        $stmt->bindParam(':gemini_api_key', $data['gemini_api_key']);
        
        if (isset($data['logo'])) {
            $stmt->bindParam(':logo', $data['logo']);
        }
        
        return $stmt->execute();
    }

    public function getNamaToko() {
        $query = "SELECT nama_toko FROM " . $this->table_name . " WHERE id = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nama_toko'] ?? 'Amar Cell Service';
    }

    public function getWhatsAppNumber() {
        $query = "SELECT nomor_whatsapp FROM " . $this->table_name . " WHERE id = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['nomor_whatsapp'] ?? '';
    }

    public function getGeminiKey() {
        $query = "SELECT gemini_api_key FROM " . $this->table_name . " WHERE id = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['gemini_api_key'] ?? null;
    }

    public function updateGeminiKey($key) {
        $query = "UPDATE " . $this->table_name . " SET gemini_api_key = :gemini_api_key WHERE id = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':gemini_api_key', $key);
        return $stmt->execute();
    }
}
?>