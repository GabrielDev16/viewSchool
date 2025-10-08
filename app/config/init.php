<?php

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/database.php';

// Define base URL
define('BASE_URL', 'http://localhost/home/ubuntu/gestctt_new/public/'); // Adjust this based on your server configuration

// Autoload classes (if using a more complex structure with classes)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../models/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
    $file = __DIR__ . '/../controllers/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

?>
