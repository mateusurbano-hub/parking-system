<?php
session_start();

echo "<h1>🔍 Análise das Variáveis de Sessão</h1>";
echo "<style>body{font-family:Arial;padding:20px;} pre{background:#f5f5f5;padding:15px;border:1px solid #ddd;}</style>";

echo "<h2>1. Variáveis Atualmente na Sessão</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<hr>";

echo "<h2>2. Verificando entrada.php</h2>";
$codigo_entrada = file_get_contents('entrada.php');

// Procura por session
if (preg_match("/\\$_SESSION\['([^']+)'\]/", $codigo_entrada, $matches)) {
    echo "<p><strong>entrada.php</strong> verifica: <code>\$_SESSION['" . $matches[1] . "']</code></p>";
} else {
    echo "<p>Não encontrei verificação de sessão em entrada.php</p>";
}

echo "<h3>Primeiras linhas do entrada.php:</h3>";
echo "<pre>" . htmlspecialchars(substr($codigo_entrada, 0, 500)) . "</pre>";

echo "<hr>";

echo "<h2>3. Verificando saida.php</h2>";
$codigo_saida = file_get_contents('saida.php');
echo "<pre>" . htmlspecialchars(substr($codigo_saida, 0, 500)) . "</pre>";

echo "<hr>";

echo "<h2>4. Verificando ativos.php</h2>";
$codigo_ativos = file_get_contents('ativos.php');
echo "<pre>" . htmlspecialchars(substr($codigo_ativos, 0, 500)) . "</pre>";
?>