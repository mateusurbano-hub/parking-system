<?php
require 'config.php';

// salvar alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && $_POST['id'] !== '') {
        // update
        $stmt = $pdo->prepare("
            UPDATE tabela_precos
            SET tipo_veiculo = :tipo,
                cliente_tipo = :cliente,
                min_dias = :min_dias,
                max_dias = :max_dias,
                valor_diaria = :valor
            WHERE id = :id
        ");
        $stmt->execute([
            ':tipo'    => $_POST['tipo_veiculo'],
            ':cliente' => $_POST['cliente_tipo'],
            ':min_dias'=> (int)$_POST['min_dias'],
            ':max_dias'=> $_POST['max_dias'] !== '' ? (int)$_POST['max_dias'] : null,
            ':valor'   => (float)str_replace(',', '.', $_POST['valor_diaria']),
            ':id'      => (int)$_POST['id'],
        ]);
    } else {
        // insert
        $stmt = $pdo->prepare("
            INSERT INTO tabela_precos (tipo_veiculo, cliente_tipo, min_dias, max_dias, valor_diaria)
            VALUES (:tipo, :cliente, :min_dias, :max_dias, :valor)
        ");
        $stmt->execute([
            ':tipo'    => $_POST['tipo_veiculo'],
            ':cliente' => $_POST['cliente_tipo'],
            ':min_dias'=> (int)$_POST['min_dias'],
            ':max_dias'=> $_POST['max_dias'] !== '' ? (int)$_POST['max_dias'] : null,
            ':valor'   => (float)str_replace(',', '.', $_POST['valor_diaria']),
        ]);
    }

    header('Location: precos.php');
    exit;
}

// delete
if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $pdo->prepare("DELETE FROM tabela_precos WHERE id = :id")->execute([':id' => $id]);
    header('Location: precos.php');
    exit;
}

// listar
$stmt = $pdo->query("SELECT * FROM tabela_precos ORDER BY tipo_veiculo, cliente_tipo, min_dias");
$regras = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tabela de preços</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="entrada.php">Entrada</a>
        <a href="saida.php">Saída</a>
        <a href="ativos.php">Ativos</a>
    </div>

    <div class="card">
        <h1>Tabela de preços</h1>

        <table>
            <tr>
                <th>Veículo</th>
                <th>Cliente</th>
                <th>Mín. dias</th>
                <th>Máx. dias</th>
                <th>Valor diária (R$)</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($regras as $r): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['tipo_veiculo']); ?></td>
                    <td><?php echo htmlspecialchars($r['cliente_tipo']); ?></td>
                    <td><?php echo (int)$r['min_dias']; ?></td>
                    <td><?php echo $r['max_dias'] !== null ? (int)$r['max_dias'] : '∞'; ?></td>
                    <td><?php echo number_format($r['valor_diaria'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="precos.php?edit=<?php echo $r['id']; ?>">Editar</a> |
                        <a href="precos.php?del=<?php echo $r['id']; ?>" onclick="return confirm('Excluir esta regra?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <hr>

        <?php
        $editRegra = null;
        if (isset($_GET['edit'])) {
            foreach ($regras as $r) {
                if ($r['id'] == (int)$_GET['edit']) {
                    $editRegra = $r;
                    break;
                }
            }
        }
        ?>

        <h2><?php echo $editRegra ? 'Editar regra' : 'Nova regra'; ?></h2>

        <form method="post">
            <?php if ($editRegra): ?>
                <input type="hidden" name="id" value="<?php echo (int)$editRegra['id']; ?>">
            <?php endif; ?>

            <label>Tipo de veículo</label>
            <select name="tipo_veiculo" required>
                <option value="CARRO"  <?php echo $editRegra && $editRegra['tipo_veiculo']=='CARRO' ? 'selected' : ''; ?>>Carro</option>
                <option value="ONIBUS" <?php echo $editRegra && $editRegra['tipo_veiculo']=='ONIBUS' ? 'selected' : ''; ?>>Ônibus</option>
            </select>

            <label>Tipo de cliente</label>
            <select name="cliente_tipo">
            <option value="NORMAL"   <?php echo $editRegra && $editRegra['cliente_tipo']=='NORMAL'   ? 'selected' : ''; ?>>Normal</option>
            <option value="PARCEIRO" <?php echo $editRegra && $editRegra['cliente_tipo']=='PARCEIRO' ? 'selected' : ''; ?>>Parceiro</option>
            </select>
            <label>Mínimo de dias</label>
            <input type="number" name="min_dias" min="1" required value="<?php echo $editRegra ? (int)$editRegra['min_dias'] : 1; ?>">

            <label>Máximo de dias (deixe vazio para ∞)</label>
            <input type="number" name="max_dias" min="1" value="<?php echo $editRegra && $editRegra['max_dias'] !== null ? (int)$editRegra['max_dias'] : ''; ?>">

            <label>Valor da diária (R$)</label>
            <input type="text" name="valor_diaria" required value="<?php echo $editRegra ? number_format($editRegra['valor_diaria'], 2, ',', '.') : ''; ?>">

            <input type="submit" value="Salvar">
        </form>
    </div>
</div>
</body>
</html>
