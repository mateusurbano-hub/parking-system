<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die('Acesso negado. <a href="login.php">Fazer login</a>');
}

require_once "config.php";

echo "<h1>🔍 Teste Completo do Sistema</h1>";
echo "<style>
    body{font-family:Arial;padding:20px;} 
    .ok{color:green;font-weight:bold;} 
    .erro{color:red;font-weight:bold;}
    .warning{color:orange;font-weight:bold;}
    pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow:auto;}
</style>";

// ========== TESTE 1: ARQUIVOS ==========
echo "<h2>1. Arquivos PHP</h2>";
$arquivos = ['entrada.php', 'saida.php', 'ativos.php'];
foreach ($arquivos as $arq) {
    if (file_exists($arq)) {
        echo "✅ <strong>$arq</strong> existe<br>";
        
        // Testa se o arquivo tem erros de sintaxe
        $output = [];
        $return_var = 0;
        exec("php -l $arq 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "&nbsp;&nbsp;&nbsp;<span class='ok'>Sintaxe OK</span><br>";
        } else {
            echo "&nbsp;&nbsp;&nbsp;<span class='erro'>ERRO DE SINTAXE:</span><br>";
            echo "<pre>" . implode("\n", $output) . "</pre>";
        }
    } else {
        echo "❌ <strong>$arq</strong> NÃO existe<br>";
    }
}

// ========== TESTE 2: TABELA NO BANCO ==========
echo "<hr><h2>2. Tabela 'veiculos' no Banco</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'veiculos'");
    $existe = $stmt->fetch();
    
    if ($existe) {
        echo "✅ <span class='ok'>Tabela 'veiculos' EXISTE</span><br><br>";
        
        // Mostra estrutura
        echo "<strong>Estrutura da tabela:</strong><br>";
        $stmt = $pdo->query("DESCRIBE veiculos");
        $colunas = $stmt->fetchAll();
        
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;margin-top:10px;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
        foreach ($colunas as $col) {
            echo "<tr>";
            echo "<td><strong>" . $col['Field'] . "</strong></td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . $col['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Conta registros
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM veiculos");
        $total = $stmt->fetch();
        echo "<br>Total de registros: <strong>" . $total['total'] . "</strong><br>";
        
    } else {
        echo "❌ <span class='erro'>Tabela 'veiculos' NÃO EXISTE</span><br><br>";
        
        echo "<h3>📝 Solução: Execute esta query no phpMyAdmin:</h3>";
        echo "<textarea style='width:100%;height:300px;font-family:monospace;font-size:12px;'>CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL,
    modelo VARCHAR(100),
    cor VARCHAR(50),
    entrada_em DATETIME NOT NULL,
    saida_em DATETIME NULL,
    valor_pago DECIMAL(10,2) NULL,
    usuario_id INT NOT NULL,
    usuario_saida_id INT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_placa (placa),
    INDEX idx_saida (saida_em),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (usuario_saida_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;</textarea>";
    }
    
} catch (Exception $e) {
    echo "❌ <span class='erro'>ERRO ao verificar tabela:</span> " . $e->getMessage() . "<br>";
}

// ========== TESTE 3: TESTE DE ACESSO AOS ARQUIVOS ==========
echo "<hr><h2>3. Teste de Acesso Direto</h2>";
echo "<p>Clique nos links abaixo para testar se os arquivos carregam:</p>";
echo "<ul>";
echo "<li><a href='entrada.php' target='_blank'>entrada.php</a></li>";
echo "<li><a href='saida.php' target='_blank'>saida.php</a></li>";
echo "<li><a href='ativos.php' target='_blank'>ativos.php</a></li>";
echo "</ul>";
echo "<p><em>Se algum arquivo mostrar erro, copie a mensagem de erro completa.</em></p>";

echo "<hr>";
echo "<a href='index.php'>← Voltar</a>";
?>