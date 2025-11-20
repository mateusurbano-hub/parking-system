<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

include "header.php";
include "funcoes.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Entrada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Registrar entrada</h1>

        <form action="entrada_salvar.php" method="POST" enctype="multipart/form-data">
            <label for="foto_placa">Foto da placa</label>
            <input
                type="file"
                id="foto_placa"
                name="foto_placa"
                accept="image/*"
                capture="environment"
            >

            <small id="status_placa" class="status-placa"></small>

            <label for="placa">Placa *</label>
            <input
                type="text"
                id="placa"
                name="placa"
                required
                placeholder="ABC1D23 ou ABC1234"
            >

            <label for="cliente">Nome do cliente</label>
            <input type="text" id="cliente" name="cliente">

            <label for="modelo">Modelo</label>
            <input type="text" id="modelo" name="modelo">

            <label for="cor">Cor</label>
            <input type="text" id="cor" name="cor">

            <label for="obs">Observações</label>
            <textarea id="obs" name="obs"></textarea>

            <button type="submit" class="btn">Registrar entrada</button>
        </form>
    </div>
</div>

<script>
// Referências aos elementos
const inputFoto   = document.getElementById('foto_placa');
const inputPlaca  = document.getElementById('placa');
const statusPlaca = document.getElementById('status_placa');

// Quando o usuário tirar a foto / escolher o arquivo
inputFoto.addEventListener('change', async () => {
    const file = inputFoto.files[0];
    if (!file) return;

    statusPlaca.textContent = 'Lendo placa, aguarde...';

    const formData = new FormData();
    formData.append('foto_placa', file);

    try {
        const resp = await fetch('ocr_placa.php', {
            method: 'POST',
            body: formData
        });

        if (!resp.ok) {
            throw new Error('Resposta HTTP inválida');
        }

        const data = await resp.json();

        if (data.placa) {
            inputPlaca.value = data.placa;
            statusPlaca.textContent = 'Placa reconhecida automaticamente.';
        } else {
            statusPlaca.textContent = data.erro || 'Não foi possível reconhecer a placa.';
        }
    } catch (e) {
        console.error(e);
        statusPlaca.textContent = 'Erro ao tentar reconhecer a placa.';
    }
});
</script>

</body>
</html>
