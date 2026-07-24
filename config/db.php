<?php
// config/db.php
// Database configuration for Arunella System

class Database {
    private $host = "localhost";
    private $db_name = "arunella";
    private $username = "root";
    private $password = ""; // Default empty password for WampServer
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("set names utf8mb4");
        } catch (PDOException $exception) {
            // In case of error, you can display a friendly message or handle it.
            // For developers, we print the exact connection error:
            die("Database Connection Error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
