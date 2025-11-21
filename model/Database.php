<?php

class Database{
    //конфигурация
    private $host = "mysql";
    private $dbname = "ai_translate";
    private $username = "root";
    private $password = "root";

    private $dsn;

    protected $conn;

    public function __construct()
    {
        $this->dsn = "mysql:host=$this->host;dbname=$this->dbname";
        $this->conn = new PDO($this->dsn,$this->username,$this->password);
    }
}