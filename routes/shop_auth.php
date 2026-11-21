<?php
declare(strict_types=1);

use Atlas\Controllers\Shop\AccountController;
use Atlas\Controllers\Shop\AddressController;
use Atlas\Controllers\Shop\AuthController;
use Atlas\Middleware\CustomerMiddleware;

/** @var \Atlas\Core\Router $router */

// Auth (Public + Logout)
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// Account Profile & Password
$router->get('/account/profile', [AccountController::class, 'profile'], [CustomerMiddleware::class]);
$router->post('/account/profile', [AccountController::class, 'updateProfile'], [CustomerMiddleware::class]);
$router->get('/account/password', [AccountController::class, 'password'], [CustomerMiddleware::class]);
$router->post('/account/password', [AccountController::class, 'updatePassword'], [CustomerMiddleware::class]);

// Address Book
$router->get('/account/addresses', [AddressController::class, 'index'], [CustomerMiddleware::class]);
$router->post('/account/addresses', [AddressController::class, 'store'], [CustomerMiddleware::class]);
$router->post('/account/addresses/{id}', [AddressController::class, 'update'], [CustomerMiddleware::class]);
$router->post('/account/addresses/{id}/delete', [AddressController::class, 'destroy'], [CustomerMiddleware::class]);
$router->post('/account/addresses/{id}/default', [AddressController::class, 'setDefault'], [CustomerMiddleware::class]);
