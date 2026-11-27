<?php
use Atlas\Controllers\Shop\{CartApiController, CartController, CheckoutController, PaymentController};
use Atlas\Middleware\CustomerMiddleware;

$customer = [CustomerMiddleware::class];
$router->get('/cart', [CartController::class, 'index']);
$router->get('/checkout', [CheckoutController::class, 'index'], $customer);
$router->post('/checkout', [CheckoutController::class, 'store'], $customer);
$router->get('/payment/{code}', [PaymentController::class, 'show'], $customer);
$router->post('/payment/{code}/simulate', [PaymentController::class, 'simulate'], $customer);
$router->get('/order/success/{code}', [PaymentController::class, 'success'], $customer);

$router->get('/api/cart', [CartApiController::class, 'get'], $customer);
$router->post('/api/cart/items', [CartApiController::class, 'add'], $customer);
$router->post('/api/cart/items/update', [CartApiController::class, 'update'], $customer);
$router->post('/api/cart/items/remove', [CartApiController::class, 'remove'], $customer);
$router->post('/api/cart/merge', [CartApiController::class, 'merge'], $customer);
$router->post('/api/cart/preview', [CartApiController::class, 'preview']);
