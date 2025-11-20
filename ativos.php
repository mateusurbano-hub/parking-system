<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "config.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veículos Ativos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Veículos Ativos</h1>

        <?php
        // Ligue erros enquanto testa
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            // Aqui uso a mesma tabela/colunas do entrada_salvar.php
            $sql = "
                SELECT
                    id,
                    placa,
                    nome_cliente,
                    modelo,
                    cor,
                    DATE_FORMAT(data_hora_entrada, '%d/%m/%Y %H:%i') AS entrada
                FROM veiculos_movimentacao
                WHERE status = 'ATIVO'
                ORDER BY data_hora_entrada DESC
            ";

            $stmt   = $pdo->query($sql);
            $lista  = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo '<p>Erro ao buscar veículos ativos: ' . htmlspecialchars($e->getMessage()) . '</p>';
            $lista = [];
        }

        if (!$lista) {
            echo "<p>Nenhum veículo ativo no momento.</p>";
        } else {
            echo "<div class='lista-ativos'>";
            foreach ($lista as $v) {
                $id      = htmlspecialchars($v['id']);
                $placa   = htmlspecialchars($v['placa']);
                $cliente = htmlspecialchars($v['nome_cliente']);
                $modelo  = htmlspecialchars($v['modelo']);
                $cor     = htmlspecialchars($v['cor']);
                $entrada = htmlspecialchars($v['entrada']);

                echo "
                <div class='ativo-item'>
                    <strong>Placa:</strong> {$placa}<br>
                    <strong>Entrada:</strong> {$entrada}<br>
                    <strong>Cliente:</strong> {$cliente}<br>
                    <strong>Modelo:</strong> {$modelo}<br>
                    <strong>Cor:</strong> {$cor}<br>
                    <br>
                    <a class='btn' href='saida.php?id={$id}'>Registrar saída</a>
                </div>
                ";
            }
            echo "</div>";
        }
        ?>

    </div>
</div>

</body>
</html>
