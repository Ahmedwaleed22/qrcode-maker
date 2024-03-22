<?php

class Dbh {
    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = '';
    private $dbName = 'qrcode_generator';

    protected function connect() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbName;
        $pdo = new PDO($dsn, $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}