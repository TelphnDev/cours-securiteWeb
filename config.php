<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('JWT_SECRET', $_ENV['JWT_SECRET']);

define('JWT_EXPIRATION', 3600);

// Connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'notes_app');
define('DB_USER', 'root');
define('DB_PASS', $_ENV['DB_PASS']);