<?php
// DEBUG TEMPORÁRIO – depois que estiver tudo ok você pode comentar essas 3 linhas
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: saida.php');
    exit;
}

$id         = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$foto_saida = trim($_POST['foto_saida'] ?? '');

if ($id <= 0) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Saída - Veículo não encontrado</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container">
        <div class="card">
            <h1>Veículo não encontrado</h1>
            <p>Nenhum veículo ativo foi localizado para esta saída.</p>
            <br>
            <a href="saida.php">Voltar para saída</a>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Buscar veículo ATIVO
$stmt = $pdo->prepare("SELECT * FROM veiculos_movimentacao WHERE id = :id AND status = 'ATIVO' LIMIT 1");
$stmt->execute([':id' => $id]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saída - Veículo não encontrado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Veículo não encontrado</h1>
        <p>Este registro já foi finalizado ou não existe mais como ativo.</p>
        <br>
        <a href="saida.php">Voltar para saída</a>
    </div>
</div>
</body>
</html>
<?php
    exit;
}

// Dados de entrada
$data_entrada   = $veiculo['data_hora_entrada'];
$tipo_veiculo   = $veiculo['tipo_veiculo']   ?? 'CARRO';
$cliente_tipo   = $veiculo['cliente_tipo']   ?? 'NORMAL';

// Definir data de saída (agora)
$data_saida = (new DateTime())->format('Y-m-d H:i:s');

// Calcular tempo total, diárias e valor
$tempo_total = calcular_tempo_total($data_entrada, $data_saida);
$num_diarias = calcular_num_diarias($data_entrada, $data_saida);
$valor_total = calcular_valor_por_diaria($pdo, $tipo_veiculo, $cliente_tipo, $num_diarias);

// Se não veio nova foto de saída, mantém a antiga (se existir)
if ($foto_saida === '' && !empty($veiculo['foto_saida'])) {
    $foto_saida = $veiculo['foto_saida'];
}

// Atualizar registro
$stmtUp = $pdo->prepare("
    UPDATE veiculos_movimentacao
    SET data_hora_saida = :saida,
        tempo_total     = :tempo_total,
        num_diarias     = :num_diarias,
        valor_total     = :valor_total,
        foto_saida      = :foto_saida,
        status          = 'FINALIZADO'
    WHERE id = :id
");
$stmtUp->execute([
    ':saida'       => $data_saida,
    ':tempo_total' => $tempo_total,
    ':num_diarias' => $num_diarias,
    ':valor_total' => $valor_total,
    ':foto_saida'  => $foto_saida,
    ':id'          => $id,
]);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saída registrada</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Saída registrada</h1>

        <p>Veículo placa <strong><?php echo htmlspecialchars($veiculo['placa']); ?></strong> teve a saída registrada.</p>

        <p><strong>Entrada:</strong> <?php echo htmlspecialchars($data_entrada); ?></p>
        <p><strong>Saída:</strong> <?php echo htmlspecialchars($data_saida); ?></p>
        <p><strong>Tempo total:</strong> <?php echo htmlspecialchars($tempo_total); ?></p>
        <p><strong>Diárias cobradas:</strong> <?php echo (int)$num_diarias; ?></p>
        <p><strong>Valor total:</strong> R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></p>

        <?php if (!empty($foto_saida)): ?>
            <p><strong>Foto de saída:</strong></p>
            <img src="<?php echo htmlspecialchars($foto_saida); ?>" class="foto-placa" alt="Foto saída">
        <?php endif; ?>

                <br>
        <a href="recibo.php?id=<?= (int)$veiculo['id']; ?>" target="_blank">
            Baixar recibo (PDF)
        </a>
        <br><br>
        <a href="saida.php">Registrar outra saída</a>
        <br><br>
        <a href="ativos.php">Ver veículos ativos</a>
    </div>
</div>
</body>
</html>
