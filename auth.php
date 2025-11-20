<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "config.php";

// Só aceita requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Lê os campos do formulário
$login = trim($_POST['login'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// Se vierem vazios, volta pro login
if ($login === '' || $senha === '') {
    header("Location: login.php?erro=1");
    exit;
}

// Busca o usuário no banco
$sql = "SELECT id, login, senha_hash, perfil, ativo 
        FROM usuarios 
        WHERE login = :login 
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['login' => $login]);
$usuario = $stmt->fetch();

// Se não achou usuário ou está inativo
if (!$usuario || (int)$usuario['ativo'] !== 1) {
    header("Location: login.php?erro=1");
    exit;
}

// Confere a senha
if (!password_verify($senha, $usuario['senha_hash'])) {
    header("Location: login.php?erro=1");
    exit;
}

// Login OK → grava na sessão (compatibilidade com ambos os padrões)
$_SESSION['user_id']        = $usuario['id'];
$_SESSION['usuario_id']     = $usuario['id'];
$_SESSION['user_login']     = $usuario['login'];
$_SESSION['usuario_login']  = $usuario['login'];
$_SESSION['user_perfil']    = $usuario['perfil'];
$_SESSION['usuario_perfil'] = $usuario['perfil'];

// Redireciona pra tela inicial
header("Location: index.php");
exit;
?>