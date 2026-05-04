<?php

class Model
{
    protected PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }
}


