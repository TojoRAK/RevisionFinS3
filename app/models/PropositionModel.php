<?php

namespace app\models;

use PDO;

class PropositionModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPropositions($idUser)
    {
        $sql = "SELECT * FROM v_objet_proposition WHERE owner_id = ? OR requester_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idUser , $idUser]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}