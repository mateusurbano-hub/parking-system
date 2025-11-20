<?php
session_start();
// Se já estiver logado → envia para entrada.php
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Login</h1>

        <?php if (isset($_GET['erro'])): ?>
            <p style="color: #ff4444; text-align:center;">Login ou senha incorretos.</p>
        <?php endif; ?>

        <form method="POST" action="auth.php">
            <label>Usuário</label>
            <input type="text" name="login" required>

            <label>Senha</label>
            <input type="password" name="senha" required>

            <button type="submit" class="btn-primary">Entrar</button>
        </form>
    </div>
</div>

</body>
</html>
