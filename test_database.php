
<?php

require_once __DIR__ . '/app/Core/Database.php';

try {
    $db = Database::connect();

    echo "Database connected successfully!";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
