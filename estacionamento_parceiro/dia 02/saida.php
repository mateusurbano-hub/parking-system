<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saída de veículo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Registrar saída</h1>

        <form action="saida_buscar.php" method="POST" enctype="multipart/form-data">
            <label for="foto_placa">Foto da placa (opcional)</label>
            <input
                type="file"
                id="foto_placa"
                name="foto_placa"
                accept="image/*"
                capture="environment"
            >

            <label for="placa">Placa *</label>
            <input
                type="text"
                id="placa"
                name="placa"
                required
                placeholder="ABC1D23 ou ABC1234"
            >

            <button class="btn" type="submit">Buscar</button>
        </form>
    </div>
</div>

</body>
</html>
