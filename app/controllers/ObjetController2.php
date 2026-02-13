<?php

namespace app\controllers;

use Flight;
use app\models\ObjetModel;
use app\models\ObjetImageModel;
use app\models\CategorieModel;

class ObjetController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $idObjet = $_GET["idObjet"];
        $marge = (float)$_GET["marge"];

        $objetModel = new ObjetModel(Flight::db());
        $objets = $objetModel->getAllEstimatedObjects($userId, $idObjet, $marge);

        // Get categories
        $categorieModel = new CategorieModel(Flight::db());
        $categories = $categorieModel->getAllCategories();

        Flight::render('client/index', [
            'baseObjet' => $objetModel->getObjectById($idObjet),
            'objets' => $objets,
            'categories' => $categories,
            'ptc' => $marge
        ]);
    }
}
