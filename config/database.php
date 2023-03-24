<?php

class Database {
    private $db_host = '127.0.0.1';  
    private $db_name = 'trical';
    private $db_username = 'root';
    private $db_password = '';
    public $conn;

    //connecting to db
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" .$this->db_host . ";dbname=" . $this->db_name, $this->db_username, $this->db_password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
        } catch(PDOException $exception) {
            echo "cannot connect to database: " .$exception->getMessage();
            die();
            
        }
        return $this->conn;
    }

}

?>
