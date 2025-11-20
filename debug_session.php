<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Debug da Sessão</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;font-weight:bold;} .erro{color:red;font-weight:bold;}</style>";

echo "<h2>1. Informações da Sessão</h2>";
echo "Session ID: <code>" . session_id() . "</code><br>";
echo "Session Status: <code>" . session_status() . "</code> (2=ativa)<br><br>";

echo "<h2>2. Variáveis de Sessão</h2>";
if (empty($_SESSION)) {
    echo "<span class='erro'>❌ Sessão VAZIA - Nenhuma variável definida</span><br>";
    echo "Isso significa que o login NÃO gravou a sessão ou ela foi perdida.<br><br>";
} else {
    echo "<span class='ok'>✅ Sessão CONTÉM dados:</span><br>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
}

echo "<h2>3. Teste de Autenticação</h2>";
if (isset($_SESSION['user_id'])) {
    echo "<span class='ok'>✅ Usuário LOGADO</span><br>";
    echo "• user_id: " . $_SESSION['user_id'] . "<br>";
    echo "• user_login: " . ($_SESSION['user_login'] ?? 'não definido') . "<br>";
    echo "• user_perfil: " . ($_SESSION['user_perfil'] ?? 'não definido') . "<br><br>";
    
    echo "<h3>Você DEVERIA ter acesso ao sistema.</h3>";
    echo "<a href='index.php' style='background:#007bff;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:10px;'>Ir para index.php</a><br><br>";
} else {
    echo "<span class='erro'>❌ Usuário NÃO logado</span><br>";
    echo "A sessão não contém 'user_id'.<br><br>";
    
    echo "<h3>Problema identificado:</h3>";
    echo "O arquivo <code>auth.php</code> não está gravando a sessão corretamente.<br>";
}

echo "<h2>4. Configurações do PHP</h2>";
echo "save_path: <code>" . session_save_path() . "</code><br>";
echo "cookie_params: <pre>" . print_r(session_get_cookie_params(), true) . "</pre>";

echo "<hr>";
echo "<h2>5. Teste de Escrita na Sessão</h2>";
$_SESSION['teste'] = 'valor_teste_' . time();
echo "Gravei na sessão: <code>\$_SESSION['teste'] = '" . $_SESSION['teste'] . "'</code><br>";
echo "<a href='debug_session.php'>Recarregue esta página</a> para ver se o valor persiste.<br>";
?>