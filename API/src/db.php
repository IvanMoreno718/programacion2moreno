<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$usuario = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$charset = $_ENV['DB_CHARSET'];
$port = $_ENV['DB_PORT'];
$db = $_ENV['DB_NAME'];
$host = $_ENV['DB_HOST'];

$options = [
    PDO::ATTR_ERRMODE   =>  PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES  => false
];

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $usuario, $password, $options);
} catch(PDOException $e) {
    error_log($e->getMessage());
    exit('Error al conectarse a la base de datos.');
}

?>