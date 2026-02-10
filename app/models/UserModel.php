<?php

namespace app\models;

use PDO;
use PDOException;

class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function checkLoginClient($name, $pwd)
    {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        if ($row = $stmt->fetchColumn()) {
            // $hash = password_hash($password, PASSWORD_DEFAULT);
            // echo $hash;
            if (!password_verify($pwd, $row)) {
                return false;
            }
        }
        return $stmt->fetchColumn();
    }

}