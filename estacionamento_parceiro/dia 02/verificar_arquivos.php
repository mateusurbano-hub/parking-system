<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Acesso negado. <a href="login.php">Fazer login</a>');
}

echo "<h1>🔍 Verificação de Arquivos</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .erro{color:red;}</style>";

$arquivos_necessarios = [
    'entrada.php',
    'saida.php',
    'ativos.php',
    'admin_usuarios.php',
    'precos.php',
    'logout.php',
    'config.php',
    'style.css'
];

echo "<h2>Status dos Arquivos:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr><th>Arquivo</th><th>Status</th><th>Tamanho</th></tr>";

foreach ($arquivos_necessarios as $arquivo) {
    $existe = file_exists($arquivo);
    $tamanho = $existe ? filesize($arquivo) . ' bytes' : '-';
    $status = $existe ? "<span class='ok'>✅ Existe</span>" : "<span class='erro'>❌ NÃO existe</span>";
    
    echo "<tr>";
    echo "<td><strong>$arquivo</strong></td>";
    echo "<td>$status</td>";
    echo "<td>$tamanho</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<a href='index.php'>Voltar</a>";
?>