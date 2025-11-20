<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
require 'config.php';
require 'funcoes.php';

$stmt = $pdo->query("
    SELECT * FROM veiculos_movimentacao
    WHERE status = 'ATIVO'
    ORDER BY data_hora_entrada ASC
");
$ativos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Veículos ativos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="entrada.php">Entrada</a>
        <a href="saida.php">Saída</a>
        <a href="ativos.php" class="active">Ativos</a>
    </div>

    <div class="card">
        <h1>Veículos no pátio</h1>
        <?php if (empty($ativos)): ?>
            <p>Não há veículos ativos.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Cliente</th>
                        <th>Entrada</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ativos as $v): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($v['placa']); ?></td>
                        <td><?php echo htmlspecialchars($v['nome_cliente']); ?></td>
                        <td><?php echo htmlspecialchars($v['data_hora_entrada']); ?></td>
                        <td>
                            <!-- Atalho: abre tela de saída com a placa já preenchida via GET (se quiser evoluir depois) -->
                            <form action="saida.php" method="get" onsubmit="return false;">
                                <!-- Por enquanto só exibe a placa, você digita manual na saída -->
                                <span class="badge-ativo">ATIVO</span>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
