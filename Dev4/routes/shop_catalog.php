<?php
use Atlas\Controllers\Shop\{CatalogController, HomeController, OrderHistoryController};
use Atlas\Middleware\CustomerMiddleware;

$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [CatalogController::class, 'index']);
$router->get('/category/{slug}', [CatalogController::class, 'category']);
$router->get('/product/{slug}', [CatalogController::class, 'show']);

$customer = [CustomerMiddleware::class];
$router->get('/account/orders', [OrderHistoryController::class, 'index'], $customer);
$router->get('/account/orders/{code}', [OrderHistoryController::class, 'show'], $customer);
$router->post('/account/orders/{code}/cancel', [OrderHistoryController::class, 'cancel'], $customer);
