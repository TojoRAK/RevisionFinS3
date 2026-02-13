<?php

namespace app\controllers;

use Flight;
use app\models\ObjetModel;
use app\models\CategorieModel;

class ObjetController
{
    public function index()
    {
        $objetModel = new ObjetModel(Flight::db());
        $categorieModel = new CategorieModel(Flight::db());

        $objets = $objetModel->getAllObjets();
        $categories = $categorieModel->getAllCategories();

        Flight::render('client/index', [
            'objets' => $objets,
            'categories' => $categories
        ]);
    }

    public function show($id)
    {
        $model = new ObjetModel(Flight::db());
        $objet = $model->getObjetById($id);

        if (!$objet) {
            Flight::halt(404, "Objet non trouvé");
        }

        Flight::render('client/item_detail', [
            'objet' => $objet
        ]);
    }

    public function myObjets()
    {
        if (!isset($_SESSION['user'])) {        //mbola amboariko 
            Flight::redirect('/');
            return;
        }

        $userId = $_SESSION['user']['id'];

        $model = new \app\models\ObjetModel(Flight::db());
        $objets = $model->getObjetsByOwner($userId);

        Flight::render('client/my_items', [
            'objets' => $objets
        ]);
    }
}
