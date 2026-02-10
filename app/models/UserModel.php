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

    public function checkLoginClient($email, $pwd)
    {
        $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($row = $stmt->fetchColumn()) {
            if (!password_verify($pwd, $row)) {
                return false;
            }
        }
        return $stmt->fetchColumn();
    }

}