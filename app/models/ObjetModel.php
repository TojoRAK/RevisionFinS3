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

    public function getAllObjects()
    {
        $stmt = $this->pdo->query("
            SELECT o.*, 
                   c.name as category_name,
                   (SELECT path FROM objet_image WHERE objet_id = o.id AND is_main = 1 LIMIT 1) as main_image,
                   (SELECT COUNT(*) FROM objet_image WHERE objet_id = o.id) as image_count
            FROM objet o
            JOIN categories c ON c.id = o.category_id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getObjectsFiltered($title, $idCategorie, $excludeUserId = null)
    {
        $sql = "
            SELECT o.*,
                   c.name as category_name,
                   (SELECT path FROM objet_image WHERE objet_id = o.id AND is_main = 1 LIMIT 1) as main_image,
                   (SELECT COUNT(*) FROM objet_image WHERE objet_id = o.id) as image_count
            FROM objet o
            JOIN categories c ON c.id = o.category_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($excludeUserId)) {
            $sql .= " AND o.owner_user_id != ?";
            $params[] = (int) $excludeUserId;
        }

        $title = is_string($title) ? trim($title) : $title;
        if (!empty($title)) {
            $sql .= " AND o.title LIKE ?";
            $params[] = '%' . $title . '%';
        }

        $idCategorie = (int) ($idCategorie ?? 0);
        if ($idCategorie > 0) {
            $sql .= " AND o.category_id = ?";
            $params[] = $idCategorie;
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getAvailableObjectsFiltered($excludeUserId, $title, $categoryId)
    {
        return $this->getObjectsFiltered($title, $categoryId, $excludeUserId);
    }

    public function getAllObjectsExceptUser($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT o.*, 
                   c.name as category_name,
                   (SELECT path FROM objet_image WHERE objet_id = o.id AND is_main = 1 LIMIT 1) as main_image,
                   (SELECT COUNT(*) FROM objet_image WHERE objet_id = o.id) as image_count
            FROM objet o
            JOIN categories c ON c.id = o.category_id
            WHERE o.owner_user_id != ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllEstimatedObjects($userId, $idObjet, $marge) // marge en %
    {
        $marge = ((float)$marge) / 100.0;

        $stmtRef = $this->pdo->prepare("
        SELECT estimated_value
        FROM objet
        WHERE id = ? AND owner_user_id = ?
        LIMIT 1
    ");
        $stmtRef->execute([$idObjet, $userId]);
        $ref = $stmtRef->fetch(PDO::FETCH_ASSOC);

        if (!$ref) {
            return [];
        }

        $price = (float)$ref['estimated_value'];

        if ($price <= 0) {
            return [];
        }

        $min = $price * (1.0 - $marge);
        $max = $price * (1.0 + $marge);

        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $stmt = $this->pdo->prepare("
        SELECT o.*,
               c.name AS category_name,
               (SELECT oi.path
                FROM objet_image oi
                WHERE oi.objet_id = o.id AND oi.is_main = 1
                LIMIT 1) AS main_image,
               (SELECT COUNT(*)
                FROM objet_image oi2
                WHERE oi2.objet_id = o.id) AS image_count
        FROM objet o
        JOIN categories c ON c.id = o.category_id
        WHERE o.owner_user_id <> ?
          AND o.id <> ?                          
          AND o.estimated_value BETWEEN ? AND ?
        ORDER BY ABS(o.estimated_value - ?) ASC, o.created_at DESC
    ");

        $stmt->execute([$userId, $idObjet, $min, $max, $price]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getObjectsByUser($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT o.*, 
                   c.name as category_name,
                   (SELECT path FROM objet_image WHERE objet_id = o.id AND is_main = 1 LIMIT 1) as main_image,
                   (SELECT COUNT(*) FROM objet_image WHERE objet_id = o.id) as image_count
            FROM objet o
            JOIN categories c ON c.id = o.category_id
            WHERE o.owner_user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getObjectById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT o.*, c.name as category_name
            FROM objet o
            LEFT JOIN categories c ON c.id = o.category_id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createObject($data)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO objet 
            (title, description, estimated_value, owner_user_id, category_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['estimated_value'],
            $data['owner_user_id'],
            $data['category_id']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function updateObject($id, $data)
    {
        $stmt = $this->pdo->prepare("
            UPDATE objet
            SET title = ?, description = ?, estimated_value = ?, category_id = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['title'],
            $data['description'],
            $data['estimated_value'],
            $data['category_id'],
            $id
        ]);
        return $stmt->rowCount() > 0;
    }

    public function deleteObject($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM objet WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
