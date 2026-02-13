<?php

namespace app\controllers;

use app\models\StatisticModel;
use Exception;
use Flight;
use flight\Engine;

class StatController
{
    protected Engine $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function showDash()
    {
        $stat = new StatisticModel(Flight::db());

        $nbObjet = $stat->countTotalObjet();

        Flight::render('/admin/dash', ["nbObjet" => $nbObjet]);
        exit;
    }
}
