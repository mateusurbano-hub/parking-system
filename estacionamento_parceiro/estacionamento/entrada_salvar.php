<?php
// MOSTRAR ERROS TEMPORARIAMENTE
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';
require 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entrada.php');
    exit;
}

// CAMPOS DO FORM
$placa          = normalizar_placa($_POST['placa'] ?? '');
$tipo_veiculo   = $_POST['tipo_veiculo'] ?? 'CARRO';
$cliente_tipo   = $_POST['cliente_tipo'] ?? 'NORMAL';
$nome_cliente   = trim($_POST['nome_cliente'] ?? '');
$modelo         = trim($_POST['modelo'] ?? '');
$cor            = trim($_POST['cor'] ?? '');
$observacoes    = trim($_POST['observacoes'] ?? '');
$data_entrada   = (new DateTime())->format('Y-m-d H:i:s');

// UPLOAD DA FOTO
$foto_entrada_caminho = '';
if (!empty($_FILES['foto_placa']['name'])) {

    $ext = strtolower(pathinfo($_FILES['foto_placa']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $permitidas)) {
        die("Tipo de arquivo não permitido.");
    }

    if (!is_dir('uploads'))        mkdir('uploads');
    if (!is_dir('uploads/entrada')) mkdir('uploads/entrada');

    $novo_nome = 'entrada_' . $placa . '_' . time() . '.' . $ext;
    $destino   = 'uploads/entrada/' . $novo_nome;

    if (move_uploaded_file($_FILES['foto_placa']['tmp_name'], $destino)) {
        $foto_entrada_caminho = $destino;
    }
}

// GRAVAR NO BANCO
$stmt = $pdo->prepare("
    INSERT INTO veiculos_movimentacao
    (placa, tipo_veiculo, cliente_tipo, nome_cliente, modelo, cor, observacoes, foto_entrada, data_hora_entrada, status)
    VALUES
    (:placa, :tipo_veiculo, :cliente_tipo, :nome_cliente, :modelo, :cor, :observacoes, :foto_entrada, :data_hora_entrada, 'ATIVO')
");

$stmt->execute([
    ':placa'            => $placa,
    ':tipo_veiculo'     => $tipo_veiculo,
    ':cliente_tipo'     => $cliente_tipo,
    ':nome_cliente'     => $nome_cliente,
    ':modelo'           => $modelo,
    ':cor'              => $cor,
    ':observacoes'      => $observacoes,
    ':foto_entrada'     => $foto_entrada_caminho,
    ':data_hora_entrada'=> $data_entrada,
]);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Entrada registrada</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Entrada registrada</h1>

        <p>Veículo com placa <strong><?php echo htmlspecialchars($placa); ?></strong> foi registrado com sucesso.</p>

        <a href="entrada.php">Nova entrada</a>
        <br><br>
        <a href="ativos.php">Ver veículos ativos</a>
    </div>
</div>
</body>
</html>
