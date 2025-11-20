<?php
session_start();

// ========== DEBUG TEMPORÁRIO - REMOVER DEPOIS ==========
error_log("========== INDEX.PHP ACESSADO ==========");
error_log("Data: " . date('Y-m-d H:i:s'));
error_log("Session ID: " . session_id());
error_log("user_id existe? " . (isset($_SESSION['user_id']) ? 'SIM (valor=' . $_SESSION['user_id'] . ')' : 'NÃO'));
error_log("Sessão completa: " . print_r($_SESSION, true));

if (!isset($_SESSION['user_id'])) {
    error_log("❌ REDIRECIONANDO PARA LOGIN (user_id não existe na sessão)");
    error_log("==========================================\n");
}
// ========== FIM DEBUG ==========

// Se NÃO estiver logado, manda para o login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estacionamento - Início</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Ícone do menu */
        .menu-icon {
            font-size: 28px;
            padding: 10px;
            cursor: pointer;
            color: white;
        }

        /* Side Menu */
        .side-menu {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -260px;
            background: #111;
            padding-top: 60px;
            transition: 0.3s;
        }

        .side-menu a {
            padding: 12px 20px;
            display: block;
            color: white;
            font-size: 18px;
            text-decoration: none;
        }

        .side-menu a:hover {
            background: #333;
        }
    </style>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("sideMenu");
            menu.style.left = (menu.style.left === "0px") ? "-260px" : "0px";
        }
    </script>

</head>
<body>

   <!-- Ícone do menu -->
<div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
<!-- Menu lateral -->
<div id="sideMenu" class="side-menu">
    <?php if (isset($_SESSION['user_perfil']) && $_SESSION['user_perfil'] === 'ADMIN'): ?>
        <a href="admin_usuarios.php">Administração</a>
        <a href="precos.php">Tabela de Preços</a>
    <?php endif; ?>

    <a href="logout.php">Sair</a>
</div>
    <div class="container">
        <div class="card">
            <h1>Estacionamento</h1>
            <p>Escolha uma opção:</p>
            <div class="nav">
                <a href="entrada.php">Entrada</a>
                <a href="saida.php">Saída</a>
                <a href="ativos.php">Ativos</a>
            </div>
        </div>
    </div>

</body>
</html>