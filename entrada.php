<?php
session_start();

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
    <title>Registrar entrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Registrar entrada</h1>

        <form action="entrada_salvar.php" method="POST" enctype="multipart/form-data">
            <!-- FOTO DA PLACA -->
            <label for="foto_placa">Foto da placa</label>
            <input
                type="file"
                id="foto_placa"
                name="foto_placa"
                accept="image/*"
                capture="environment"
            >

            <!-- TIPO DE VEÍCULO -->
            <label for="tipo_veiculo">Tipo de veículo *</label>
            <select id="tipo_veiculo" name="tipo_veiculo" required>
                <option value="CARRO">Carro</option>
                <option value="ONIBUS">Ônibus</option>
            </select>

            <!-- TIPO DE CLIENTE -->
            <label for="cliente_tipo">Tipo de cliente *</label>
            <select id="cliente_tipo" name="cliente_tipo" required>
                <option value="NORMAL">Normal</option>
                <option value="PARCEIRO">Parceiro</option>
            </select>

            <!-- PLACA -->
            <label for="placa">Placa *</label>
            <input
                type="text"
                id="placa"
                name="placa"
                required
                placeholder="ABC1D23 ou ABC1234"
            >

            <!-- NOME DO CLIENTE -->
            <label for="nome_cliente">Nome do cliente</label>
            <input
                type="text"
                id="nome_cliente"
                name="nome_cliente"
            >

            <!-- MODELO -->
            <label for="modelo">Modelo</label>
            <input
                type="text"
                id="modelo"
                name="modelo"
            >

            <!-- COR -->
            <label for="cor">Cor</label>
            <input
                type="text"
                id="cor"
                name="cor"
            >

            <!-- OBSERVAÇÕES -->
            <label for="observacoes">Observações</label>
            <textarea
                id="observacoes"
                name="observacoes"
                rows="3"
            ></textarea>

            <button class="btn" type="submit">Registrar entrada</button>
        </form>
    </div>
</div>

</body>
</html>
