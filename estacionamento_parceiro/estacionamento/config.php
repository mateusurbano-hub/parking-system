<?php
$host = 'localhost';
$db   = 'inovalive_parking_db';   // exatamente como aparece no MySQL
$user = 'inovalive_parking';      // exatamente como aparece no MySQL
$pass = 'Suetam@2025';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
} catch (PDOException $e) {
    die('Erro na conexão com o banco de dados: ' . $e->getMessage());
}

$TARIFA_POR_HORA = 8.00;
$VALOR_MINIMO    = 5.00;
