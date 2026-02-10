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

    public function checkLoginAdmin($email, $pwd)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? and role=\"ADMIN\" LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetchColumn();
        if($user) {
            if (!password_verify($pwd, $user['password_hash'])) {
                return false;
            }
        }
        return $user;
    }

}