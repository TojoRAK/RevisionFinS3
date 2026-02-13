<?php

// use Flight;

use app\controllers\AdminLogController;
use app\controllers\CategorieController;
use app\controllers\PropositionController;

use app\controllers\AuthClient;
use app\controllers\ObjetController;
use app\controllers\TradeController;
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

	$router->get('/index', [ObjetController::class, 'index']);

	$router->get('/objet/@id:[0-9]+', [ObjetController::class, 'show']);

	$router->get('/my-objets', [ObjetController::class, 'myObjets']);

	$router->group('/objets', function () use ($router) {
		$router->get('/list', [ObjetController::class, 'list']);
		$router->post('', [ObjetController::class, 'create']);
		$router->post('/@id:[0-9]+', [ObjetController::class, 'update']);
		$router->delete('/@id:[0-9]+', [ObjetController::class, 'delete']);
	});



	$router->group('/auth', function () use ($router) {
		$router->get('/register', function () {
			Flight::render('client/register');
		});
		$router->post('/login', [AuthClient::class, 'doLogin']);
	});
	$router->group('/propositions', function () use ($router) {
		$router->get('/list', [PropositionController::class, 'getReceivedPropositions']);
		$router->post('/@id:[0-9]+/accept', [TradeController::class, 'accept']);
		$router->post('/@id:[0-9]+/reject', [TradeController::class, 'reject']);
		$router->post('/@id:[0-9]+/cancel', [TradeController::class, 'cancel']);
	});

	$router->group('/trade', function () use ($router) {
		$router->post('/request', [TradeController::class, 'makeRequest']);
	});


	$router->group('/auth', function () use ($router) {
		$router->get('/register', function () {
			Flight::render('client/register');
		});
		$router->post('/register', [AuthClient::class, 'validateInputAndLogin']);
	});


	//==============ADMIN================//
	$router->group('/admin', function () use ($router) {
		$router->get('/login', function () {
			Flight::render('admin/login');
		});

		$router->post('/login', [AdminLogController::class, 'doLogin']);

		$router->get('/', function () {
			Flight::redirect('/admin/login');
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
