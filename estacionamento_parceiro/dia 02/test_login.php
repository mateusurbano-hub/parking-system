<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Diagnóstico do Sistema de Login</h1>";
echo "<style>body{font-family:Arial;padding:20px;} h2{color:#333;border-bottom:2px solid #007bff;padding-bottom:5px;} .ok{color:green;font-weight:bold;} .erro{color:red;font-weight:bold;}</style>";

// 1. Verifica PHP
echo "<h2>1. PHP</h2>";
echo "✅ Versão: " . phpversion() . "<br><br>";

// 2. Testa conexão com banco
echo "<h2>2. Banco de Dados</h2>";
try {
    require_once "config.php";
    echo "✅ Conexão estabelecida<br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $total = $stmt->fetch();
    echo "✅ Total de usuários cadastrados: <strong>" . $total['total'] . "</strong><br><br>";
    
} catch (Exception $e) {
    echo "<span class='erro'>❌ ERRO: " . $e->getMessage() . "</span><br><br>";
    die();
}

// 3. Testa busca do usuário admin
echo "<h2>3. Usuário 'admin'</h2>";
try {
    $stmt = $pdo->prepare("SELECT id, login, senha_hash, LENGTH(senha_hash) as tam, perfil, ativo FROM usuarios WHERE login = ?");
    $stmt->execute(['admin']);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo "<span class='erro'>❌ Usuário 'admin' NÃO ENCONTRADO no banco!</span><br>";
        echo "Execute esta query no phpMyAdmin:<br>";
        echo "<code>SELECT * FROM usuarios WHERE login LIKE '%admin%';</code><br><br>";
        die();
    }
    
    echo "✅ Usuário encontrado<br>";
    echo "• <strong>ID:</strong> " . $usuario['id'] . "<br>";
    echo "• <strong>Login:</strong> " . $usuario['login'] . "<br>";
    echo "• <strong>Perfil:</strong> " . $usuario['perfil'] . "<br>";
    echo "• <strong>Ativo:</strong> " . ($usuario['ativo'] ? '<span class="ok">SIM</span>' : '<span class="erro">NÃO</span>') . "<br>";
    echo "• <strong>Tamanho hash:</strong> " . $usuario['tam'] . " caracteres<br>";
    echo "• <strong>Início do hash:</strong> " . substr($usuario['senha_hash'], 0, 30) . "...<br><br>";
    
    // 4. TESTE CRÍTICO: Valida a senha
    echo "<h2>4. ⚡ TESTE CRÍTICO: Validação da Senha '123456'</h2>";
    
    $senha_teste = '123456';
    $hash_banco = $usuario['senha_hash'];
    
    echo "Senha testada: <code>" . $senha_teste . "</code><br>";
    echo "Hash completo: <code style='font-size:10px;word-break:break-all;'>" . $hash_banco . "</code><br><br>";
    
    // Testa password_verify
    $resultado = password_verify($senha_teste, $hash_banco);
    
    if ($resultado) {
        echo "🎉 <span class='ok' style='font-size:20px;'>SENHA CORRETA!</span><br><br>";
        echo "<strong>Conclusão:</strong> A senha no banco está OK.<br>";
        echo "O problema está no arquivo <code>auth.php</code> (lógica de validação ou sessão).<br><br>";
        
        echo "<h3>Próximo passo:</h3>";
        echo "Substitua o <code>auth.php</code> pela versão com logs que te passei.<br>";
        
    } else {
        echo "❌ <span class='erro' style='font-size:20px;'>SENHA INCORRETA!</span><br><br>";
        echo "<strong>Problema:</strong> O hash no banco NÃO corresponde a '123456'.<br><br>";
        
        echo "<h3>Solução:</h3>";
        echo "Gere nova hash e atualize no banco:<br><br>";
        
        // Gera hash correta
        $nova_hash = password_hash($senha_teste, PASSWORD_DEFAULT);
        echo "1. Copie esta hash:<br>";
        echo "<textarea style='width:100%;height:80px;font-family:monospace;'>" . $nova_hash . "</textarea><br><br>";
        
        echo "2. Execute no phpMyAdmin:<br>";
        echo "<textarea style='width:100%;height:100px;font-family:monospace;'>UPDATE usuarios 
SET senha_hash = '" . $nova_hash . "'
WHERE login = 'admin';</textarea><br><br>";
        
        echo "3. Depois teste o login novamente.<br>";
    }
    
} catch (Exception $e) {
    echo "<span class='erro'>❌ ERRO: " . $e->getMessage() . "</span><br>";
}

echo "<hr><p style='color:#666;font-size:12px;'>Após corrigir, delete este arquivo por segurança.</p>";
?>