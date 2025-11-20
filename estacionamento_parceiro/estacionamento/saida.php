<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
require 'config.php';
require 'funcoes.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saída de veículo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="entrada.php">Entrada</a>
        <a href="saida.php" class="active">Saída</a>
        <a href="ativos.php">Ativos</a>
    </div>

    <div class="card">
        <h1>Registrar saída</h1>
        <form action="saida_buscar.php" method="post" enctype="multipart/form-data">
            <label for="foto_placa">Foto da placa (opcional)</label>
            <input type="file" name="foto_placa" id="foto_placa" accept="image/*" capture="environment">

            <label for="placa">Placa *</label>
            <input type="text" name="placa" id="placa" required placeholder="ABC1D23 ou ABC1234">

            <input type="submit" value="Buscar veículo">
        </form>
    </div>
</div>
</body>
</html>
