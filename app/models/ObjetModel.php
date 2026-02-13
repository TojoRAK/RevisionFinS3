<?php

namespace app\models;

use PDO;

class ObjetModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllObjets()
    {
        $stmt = $this->pdo->query("
            SELECT o.id,
                   o.title,
                   o.description,
                   o.estimated_value,
                   o.owner_user_id,
                   o.category_id,
                   o.created_at,
                   c.name AS category_name
            FROM objet o
            JOIN categories c ON o.category_id = c.id
            ORDER BY o.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getObjetById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT o.*,
                   c.name AS category_name
            FROM objet o
            JOIN categories c ON o.category_id = c.id
            WHERE o.id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getObjetsByOwner($userId)
    {
        $stmt = $this->pdo->prepare("
        SELECT o.*,
               c.name AS category_name
        FROM objet o
        JOIN categories c ON o.category_id = c.id
        WHERE o.owner_user_id = ?
        ORDER BY o.created_at DESC
    ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
