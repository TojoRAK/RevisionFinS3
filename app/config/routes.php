<?php

// use Flight;
use app\controllers\CategorieController;
use app\controllers\AuthClient;
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



    $router->group('/auth', function () use ($router) {
        $router->get('/register', function () {
            Flight::render('client/register');
        });
        $router->post('/login' , [AuthClient::class , 'doLogin']);
    });


	$router->group('/auth', function () use ($router) {
		$router->get('/register', function () {
			Flight::render('client/register');
		});
        $router->post('/register' , [AuthClient::class , 'validateInputAndLogin']);
	});


	//==============ADMIN================//
	$router->group('/admin', function () use ($router) {
		$router->get('/', function () {
			Flight::render('admin/login');
		});
		$router->get('/categories', function () {
			Flight::render('admin/categories'); // points to views/admin/categories.php
		});
		$router->group('/categories', function () use ($router) {
			$router->get('/list', [CategorieController::class, 'getAllCategories']);
			$router->get('/@id:[0-9]+', [CategorieController::class, 'getCategory']);
			$router->post('', [CategorieController::class, 'createCategory']);
			$router->post('/@id:[0-9]+', [CategorieController::class, 'updateCategory']);
			$router->delete('/@id:[0-9]+', [CategorieController::class, 'deleteCategory']);
		});
	});
}, [SecurityHeadersMiddleware::class]);
