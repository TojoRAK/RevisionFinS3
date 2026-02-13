<?php

namespace app\controllers;

use app\models\PropositionModel;
use Flight;
use app\models\CategorieModel;

class PropositionController
{
    private $app;

    public function __construct($app = null)
    {
        $this->app = $app;
    }
    public function getReceivedPropositions()
    {
        try {
            $model = new PropositionModel(Flight::db());
            if ($_SESSION['user'] == null) {
                Flight::redirect('/');
                return;
            }
            $propositions = $model->getPropositions($_SESSION['user']['id']);
            Flight::render('client/propositions', [
                'propositions' => $propositions
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
        }
    }



}