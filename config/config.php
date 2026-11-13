<?php
$config = [
    'database' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'atlas',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
$localConfigPath = __DIR__ . '/config.local.php';
if (file_exists($localConfigPath)) {
    $localConfig = require $localConfigPath;
    $config = array_replace_recursive($config, $localConfig);
}
return $config;