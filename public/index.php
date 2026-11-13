<?php
require_once __DIR__ . '/../app/Core/Router.php';
$router = require __DIR__ . '/../routes/web.php';
$router->dispatch();