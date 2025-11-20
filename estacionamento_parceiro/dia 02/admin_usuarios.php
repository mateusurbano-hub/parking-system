<?php
require "auth.php";
require "config.php";

// Garante que a sessão tenha as chaves certas
if (!isset($_SESSION['user_perfil']) || $_SESSION['user_perfil'] !== 'ADMIN') {
    die("<h2>Acesso negado</h2>");
}

$erro = "";
$ok   = "";

// Criar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar'])) {
    $nome   = trim($_POST['nome']  ?? '');
    $login  = trim($_POST['login'] ?? '');
    $perfil = $_POST['perfil'] ?? 'OPERADOR';

    if ($nome === "" || $login === "") {
        $erro = "Preencha todos os campos.";
    } else {
        $hash = password_hash("123456", PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nome, login, senha_hash, perfil, ativo)
                VALUES (:nome, :login, :senha, :perfil, 1)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':nome'   => $nome,
                ':login'  => $login,
                ':senha'  => $hash,
                ':perfil' => $perfil
            ]);
            $ok = "Usuário criado com sucesso! Senha inicial: 123456";
        } catch (PDOException $e) {
            // Durante desenvolvimento: mostre o erro real
            // Depois, você pode voltar a mensagem amigável.
            $erro = "Erro ao criar usuário: " . $e->getMessage();
        }
    }
}

// Resetar senha
if (isset($_GET['reset'])) {
    $id   = (int)$_GET['reset'];
    $hash = password_hash("123456", PASSWORD_BCRYPT);

    $pdo->prepare("UPDATE usuarios SET senha_hash = :s WHERE id = :id")
        ->execute([':s' => $hash, ':id' => $id]);

    $ok = "Senha resetada para: 123456";
}

// Ativar / Desativar
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    $pdo->prepare("UPDATE usuarios SET ativo = NOT ativo WHERE id = :id")
        ->execute([':id' => $id]);

    $ok = "Status de usuário atualizado.";
}

// Listagem
$lista = $pdo->query("SELECT * FROM usuarios ORDER BY nome")
             ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Usuários - Administração</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Gerenciar Usuários</h1>

        <?php if ($erro): ?>
            <p style="color:#ff8080;"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>

        <?php if ($ok): ?>
            <p style="color:#80ff80;"><?= htmlspecialchars($ok) ?></p>
        <?php endif; ?>

        <h2>Criar novo usuário</h2>

        <form method="post">
            <input type="hidden" name="criar" value="1">

            <label>Nome</label>
            <input type="text" name="nome" required>

            <label>Login</label>
            <input type="text" name="login" required>

            <label>Perfil</label>
            <select name="perfil">
                <option value="OPERADOR">Operador</option>
                <option value="ADMIN">Administrador</option>
            </select>

            <button class="btn" type="submit">Criar usuário</button>
        </form>

        <hr>

        <h2>Usuários cadastrados</h2>

        <table class="tabela">
            <tr>
                <th>Nome</th>
                <th>Login</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>

            <?php foreach ($lista as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['login']) ?></td>
                    <td><?= htmlspecialchars($u['perfil']) ?></td>
                    <td><?= $u['ativo'] ? "Ativo" : "Inativo" ?></td>
                    <td>
                        <a href="?reset=<?= (int)$u['id'] ?>">Resetar senha</a> |
                        <a href="?toggle=<?= (int)$u['id'] ?>">Ativar/Desativar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <br>
        <a href="index.php">Voltar</a>

    </div>
</div>

</body>
</html>
