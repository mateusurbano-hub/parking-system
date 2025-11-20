<?php
date_default_timezone_set('America/Sao_Paulo');
$host = 'localhost';
$db   = 'inovalive_parking_db';
$user = 'inovalive_parking';
$pass = 'Suetam@2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
