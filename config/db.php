<?php
$host = 'localhost';         // or the container name if you're using Docker
$db   = 'n8n';     // change to your database name
$user = 'n8nuser';         // change to your MySQL username
$pass = 'Plant32!';     // change to your MySQL password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // return results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage();
    exit;
}
