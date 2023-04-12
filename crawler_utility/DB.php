<?php
require 'index.php';
//connect dtabasea
class DB {
    protected $conn;
    public function __construct($host, $username, $password, $dbname) {
        $this->conn = mysqli_connect($host, $username, $password, $dbname);
    }

    public function query($sql) {
        try {
            return mysqli_query($this->conn, $sql);
        } catch (mysqli_sql_exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function escape($value) {
        return mysqli_real_escape_string($this->conn, $value);
    }
}