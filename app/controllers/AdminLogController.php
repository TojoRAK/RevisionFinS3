<?php

namespace app\controllers;

use app\models\CategorieModel;
use app\models\UserModel;
use Exception;
use Flight;
use flight\Engine;

class AdminLogController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function doLogin()
    {
        header('Content-Type: application/json; charset=utf-8');
        $email = trim($_POST['email']);
        $pwd = trim($_POST['password']);

        try {

            $model = new UserModel(Flight::db());

            $errors = "";
            $user = $model->checkLoginClient($email, $pwd);

            if (!$user) {
                $errors = "Mot de passe incorrect";
            }

            if (!empty($errors)) {
                echo json_encode([
                    'ok' => false,
                    'errors' => $errors
                ]);
                return;
            }
            echo json_encode(
                [
                    'ok' => true,
                ]
            );

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'errors' => $e->getMessage()
            ]);
        }
    }


}