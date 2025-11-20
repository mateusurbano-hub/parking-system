<?php
// DEBUG – remover depois
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: saida.php');
    exit;
}

// Normaliza placa: remove traços, espaços e deixa maiúscula
function normalizar_placa(string $placa): string {
    return strtoupper(str_replace(['-', ' '], '', trim($placa)));
}
// BUSCAR VEÍCULO ATIVO
$stmt = $pdo->prepare("SELECT * FROM veiculos_movimentacao WHERE placa = :placa AND status = 'ATIVO' LIMIT 1");
$stmt->execute([':placa' => $placa]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    die("<h1 style='color:white;'>Nenhum veículo ativo encontrado com essa placa.</h1>");
}

// PROCESSAR FOTO DE SAÍDA
$foto_saida_caminho = '';
if (!empty($_FILES['foto_placa']['name'])) {

    $ext = strtolower(pathinfo($_FILES['foto_placa']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $permitidas)) {
        die("Tipo de arquivo não permitido.");
    }

    if (!is_dir('uploads'))        mkdir('uploads');
    if (!is_dir('uploads/saida')) mkdir('uploads/saida');

    $novo_nome = 'saida_' . $placa . '_' . time() . '.' . $ext;
    $destino   = 'uploads/saida/' . $novo_nome;

    if (move_uploaded_file($_FILES['foto_placa']['tmp_name'], $destino)) {
        $foto_saida_caminho = $destino;
    }
}

// CALCULAR TEMPO, DIÁRIAS E VALOR
$data_entrada = $veiculo['data_hora_entrada'];
$data_saida   = (new DateTime())->format('Y-m-d H:i:s');

$tempo_total   = calcular_tempo_total($data_entrada, $data_saida);
$num_diarias   = calcular_num_diarias($data_entrada, $data_saida);

$valor_total   = calcular_valor_por_diaria(
    $pdo,
    $veiculo['tipo_veiculo'],
    $veiculo['cliente_tipo'],
    $num_diarias
);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar saída</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Confirmar saída</h1>

        <p><strong>Placa:</strong> <?php echo htmlspecialchars($placa); ?></p>
        <p><strong>Entrada:</strong> <?php echo $data_entrada; ?></p>
        <p><strong>Saída:</strong> <?php echo $data_saida; ?></p>

        <p><strong>Tempo total:</strong> <?php echo htmlspecialchars($tempo_total); ?></p>
        <p><strong>Diárias:</strong> <?php echo (int)$num_diarias; ?></p>
        <p><strong>Valor total:</strong> R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></p>

        <?php if (!empty($foto_saida_caminho)): ?>
            <p><strong>Foto de saída:</strong></p>
            <img src="<?php echo htmlspecialchars($foto_saida_caminho); ?>" class="foto-placa" alt="Foto saída">
        <?php endif; ?>

        <form action="saida_finalizar.php" method="post">
            <input type="hidden" name="id" value="<?php echo (int)$veiculo['id']; ?>">
            <input type="hidden" name="foto_saida" value="<?php echo htmlspecialchars($foto_saida_caminho); ?>">
            <input type="hidden" name="num_diarias" value="<?php echo (int)$num_diarias; ?>">
            <input type="hidden" name="valor_total" value="<?php echo $valor_total; ?>">
            <input type="submit" value="Confirmar saída e dar baixa">
        </form>

        <br>
        <a href="saida.php">Cancelar</a>
    </div>
</div>
</body>
</html>
