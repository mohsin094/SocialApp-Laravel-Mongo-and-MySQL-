<?php
namespace App\Services;

use MongoDB\Client as connection;

class DbConnection
{
    protected $conn;
    public function __construct($collection){
        $this->conn =(new connection)->socialApp->$collection;
    }

    public function getConnection(){
        return $this->conn;
    }

}
