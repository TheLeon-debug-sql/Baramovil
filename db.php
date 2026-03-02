<?php
// C:\xampp\htdocs\Baramovil\db.php

$host = 'localhost';
$port = '3329';
$db   = 'ADN_SEBAS';
$user = 'sistemas';
$pass = 'adn';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     header('Content-Type: application/json');
     echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
     exit;
}
?>
