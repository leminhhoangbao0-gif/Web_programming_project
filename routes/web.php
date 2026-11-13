<?php
$router = new Router();
$router->get('/', function () {
    echo 'Welcome to Atlas E-commerce';
});
$router->get('/admin/login', function () {
    echo 'Admin Login Page';
});
$router->get('/admin/dashboard', function () {
    echo 'Admin Dashboard';
});
$router->get('/admin/categories', function () {
    echo 'Admin Categories';
});
$router->get('/admin/products', function () {
    echo 'Admin Products';
});
return $router;
