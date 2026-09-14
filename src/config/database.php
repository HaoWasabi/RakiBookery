<?php
class Database
{
    private $host = "localhost";
    private $dbname = "bookstoredb";
    private $username = "root";
    private $password = "root";
    private $conn;
    private static $instance = null;

    public function __construct()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password,);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Database error: " . $exception->getMessage();
        }
    }
    public function getConnection()
    {
        return $this->conn;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
