<?php
namespace app\controllers;

use Flight;
use app\models\ObjetModel;
use app\models\CategorieModel;

class ObjetController
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        $objetModel = new ObjetModel(Flight::db());
        $objets = $objetModel->getAllObjectsExceptUser($userId);

        // Get categories
        $categorieModel = new CategorieModel(Flight::db());
        $categories = $categorieModel->getAllCategories();

        Flight::render('client/index', [
            'objets' => $objets,
            'categories' => $categories
        ]);
    }

    public function show($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $model = new ObjetModel(Flight::db());
        $objet = $model->getObjectById($id);

        if (!$objet) {
            Flight::halt(404, "Objet non trouvé");
        }

        Flight::render('client/item_detail', [
            'objet' => $objet
        ]);
    }

    public function myObjets()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::redirect('/');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $model = new ObjetModel(Flight::db());
        $objets = $model->getObjectsByUser($userId);

        Flight::render('client/my_items', [
            'objets' => $objets
        ]);
    }

    public function list()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $userId = $_SESSION['user']['id'];
        $data = $model->getObjectsByUser($userId);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok'   => true,
            'data' => $data
        ]);
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $input = Flight::request()->data->getData();
        $input['owner_user_id'] = $_SESSION['user']['id'];
        
        $model->createObject($input);
        Flight::json(['ok' => true, 'message' => 'Objet ajouté']);
    }

    public function update($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $input = Flight::request()->data->getData();
        
        $model->updateObject($id, $input);
        Flight::json(['ok' => true, 'message' => 'Objet mis à jour']);
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            Flight::json(['ok' => false, 'message' => 'Non authentifié'], 401);
            return;
        }

        $model = new ObjetModel(Flight::db());
        $model->deleteObject($id);
        Flight::json(['ok' => true, 'message' => 'Objet supprimé']);
    }
}