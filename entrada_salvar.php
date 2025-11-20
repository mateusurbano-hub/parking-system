<?php
// entrada_salvar.php
session_start();

require 'config.php';
require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entrada.php');
    exit;
}

// -------------------------
// 1. Lê e normaliza campos
// -------------------------

$placa_original = $_POST['placa'] ?? '';
$placa          = normalizar_placa($placa_original);

$tipo_veiculo = strtoupper(trim($_POST['tipo_veiculo'] ?? 'CARRO'));
$cliente_tipo = strtoupper(trim($_POST['cliente_tipo'] ?? 'NORMAL'));

$nome_cliente = trim($_POST['nome_cliente'] ?? '');
$modelo       = trim($_POST['modelo'] ?? '');
$cor          = trim($_POST['cor'] ?? '');
$observacoes  = trim($_POST['observacoes'] ?? '');

// valida placa
if (!validar_placa($placa)) {
    $erro = "Placa inválida: {$placa_original}";
    ?>
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Erro na entrada</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <div class="container">
        <div class="card">
            <h1>Erro ao registrar entrada</h1>
            <p><?= htmlspecialchars($erro) ?></p>
            <br>
            <a href="entrada.php">Voltar</a>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// valida tipo de veículo
$tipos_validos = ['CARRO', 'ONIBUS'];
if (!in_array($tipo_veiculo, $tipos_validos, true)) {
    $tipo_veiculo = 'CARRO';
}

// valida tipo de cliente
$clientes_validos = ['NORMAL', 'PARCEIRO'];
if (!in_array($cliente_tipo, $clientes_validos, true)) {
    $cliente_tipo = 'NORMAL';
}

// -------------------------
// 2. Upload da foto (opcional)
// -------------------------

$foto_entrada_caminho = '';

if (!empty($_FILES['foto_placa']['name'])) {
    $ext = strtolower(pathinfo($_FILES['foto_placa']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (in_array($ext, $permitidas, true)) {

        if (!is_dir('uploads'))        mkdir('uploads');
        if (!is_dir('uploads/entrada')) mkdir('uploads/entrada');

        $novo_nome = 'entrada_' . $placa . '_' . time() . '.' . $ext;
        $destino   = 'uploads/entrada/' . $novo_nome;

        if (move_uploaded_file($_FILES['foto_placa']['tmp_name'], $destino)) {
            $foto_entrada_caminho = $destino;
        }
    }
}

// -------------------------
// 3. Insere no banco
// -------------------------

$data_entrada = (new DateTime())->format('Y-m-d H:i:s');

$sql = "
    INSERT INTO veiculos_movimentacao
    (placa, nome_cliente, modelo, cor, observacoes,
     tipo_veiculo, cliente_tipo,
     data_hora_entrada, foto_entrada, status)
    VALUES
    (:placa, :nome_cliente, :modelo, :cor, :observacoes,
     :tipo_veiculo, :cliente_tipo,
     :data_entrada, :foto_entrada, 'ATIVO')
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':placa'         => $placa,
    ':nome_cliente'  => $nome_cliente,
    ':modelo'        => $modelo,
    ':cor'           => $cor,
    ':observacoes'   => $observacoes,
    ':tipo_veiculo'  => $tipo_veiculo,
    ':cliente_tipo'  => $cliente_tipo,
    ':data_entrada'  => $data_entrada,
    ':foto_entrada'  => $foto_entrada_caminho,
]);

// -------------------------
// 4. Tela de sucesso
// -------------------------
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Entrada registrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Entrada registrada</h1>

        <p>Veículo placa <strong><?= htmlspecialchars($placa) ?></strong> registrado com sucesso.</p>

        <p><strong>Tipo de veículo:</strong> <?= htmlspecialchars($tipo_veiculo) ?></p>
        <p><strong>Tipo de cliente:</strong> <?= htmlspecialchars($cliente_tipo) ?></p>
        <p><strong>Entrada:</strong> <?= htmlspecialchars($data_entrada) ?></p>

        <?php if ($foto_entrada_caminho): ?>
            <p><strong>Foto da placa:</strong></p>
            <img src="<?= htmlspecialchars($foto_entrada_caminho) ?>" class="foto-placa" alt="Foto placa">
        <?php endif; ?>

        <br>
        <a href="entrada.php">Registrar outra entrada</a><br>
        <a href="ativos.php">Ver veículos ativos</a>
    </div>
</div>
</body>
</html>
