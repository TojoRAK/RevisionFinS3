<?php

use Flight;
use app\controllers\CategorieController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) {

    $router->get('/', function () {
        Flight::render('client/login');
    });

    $router->group('/categories', function () use ($router) {

        $router->get('', [CategorieController::class, 'getAllCategories']);
        $router->get('/@id:[0-9]+', [CategorieController::class, 'getCategory']);
        $router->post('', [CategorieController::class, 'createCategory']);
        $router->post('/@id:[0-9]+', [CategorieController::class, 'updateCategory']);
        $router->delete('/@id:[0-9]+', [CategorieController::class, 'deleteCategory']);

    });

}, [SecurityHeadersMiddleware::class]);
