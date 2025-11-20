<?php
// recibo.php
session_start();
require 'config.php';
require 'funcoes.php';

// Opcional: só permitir usuário logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ID da movimentação
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("ID inválido.");
}

// Busca os dados da movimentação
$sql = "
    SELECT *
    FROM veiculos_movimentacao
    WHERE id = :id
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$mov = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mov) {
    die("Movimentação não encontrada.");
}

// Garante que já foi finalizado (opcional)
if ($mov['status'] !== 'FINALIZADO') {
    die("Esta movimentação ainda não foi finalizada.");
}

// Calcula/normaliza alguns dados
$placa        = $mov['placa'];
$tipoVeiculo  = $mov['tipo_veiculo'];
$clienteTipo  = $mov['cliente_tipo'];
$nomeCliente  = $mov['nome_cliente'];
$modelo       = $mov['modelo'];
$cor          = $mov['cor'];
$dataEntrada  = $mov['data_hora_entrada'];
$dataSaida    = $mov['data_hora_saida'];
$tempoTotal   = $mov['tempo_total'];
$numDiarias   = (int)$mov['num_diarias'];
$valorTotal   = (float)$mov['valor_total'];

// Se por algum motivo não tiver tempo_total gravado, calcula de novo
if (!$tempoTotal && $dataEntrada && $dataSaida) {
    $tempoTotal = calcular_tempo_total($dataEntrada, $dataSaida);
}

// ---------------- FPDF ----------------
require 'fpdf/fpdf.php';

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

// Cabeçalho
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Recibo de Estacionamento', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Data de emissao: ' . date('d/m/Y H:i'), 0, 1, 'R');
$pdf->Ln(4);

// Dados do estabelecimento (ajuste com o nome da empresa)
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Estacionamento InovaLive', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'CNPJ: 00.000.000/0000-00', 0, 1, 'L'); // opcional
$pdf->Cell(0, 5, 'End.: Rua Exemplo, 123 - Cidade/UF', 0, 1, 'L'); // opcional
$pdf->Ln(4);

// Linha separadora
$pdf->SetDrawColor(200, 200, 200);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(6);

// Dados do cliente / veiculo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Dados do veiculo', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Placa:', 0, 0, 'L');
$pdf->Cell(0, 6, $placa, 0, 1, 'L');

$pdf->Cell(40, 6, 'Tipo de veiculo:', 0, 0, 'L');
$pdf->Cell(0, 6, $tipoVeiculo, 0, 1, 'L');

$pdf->Cell(40, 6, 'Tipo de cliente:', 0, 0, 'L');
$pdf->Cell(0, 6, $clienteTipo, 0, 1, 'L');

$pdf->Cell(40, 6, 'Nome do cliente:', 0, 0, 'L');
$pdf->Cell(0, 6, $nomeCliente ?: '-', 0, 1, 'L');

$pdf->Cell(40, 6, 'Modelo:', 0, 0, 'L');
$pdf->Cell(0, 6, $modelo ?: '-', 0, 1, 'L');

$pdf->Cell(40, 6, 'Cor:', 0, 0, 'L');
$pdf->Cell(0, 6, $cor ?: '-', 0, 1, 'L');

$pdf->Ln(4);

// Período e valores
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Periodo e cobranca', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, 'Entrada:', 0, 0, 'L');
$pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($dataEntrada)), 0, 1, 'L');

$pdf->Cell(40, 6, 'Saida:', 0, 0, 'L');
$pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($dataSaida)), 0, 1, 'L');

$pdf->Cell(40, 6, 'Tempo total:', 0, 0, 'L');
$pdf->Cell(0, 6, $tempoTotal, 0, 1, 'L');

$pdf->Cell(40, 6, 'Diarias:', 0, 0, 'L');
$pdf->Cell(0, 6, $numDiarias . ' diaria(s)', 0, 1, 'L');

$pdf->Ln(4);

// Valor total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Valor total:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'R$ ' . number_format($valorTotal, 2, ',', '.'), 0, 1, 'L');

$pdf->Ln(10);

// Rodapé
$pdf->SetFont('Arial', '', 9);
$pdf->MultiCell(0, 4,
    "Este recibo comprova o pagamento referente ao periodo de estacionamento do veiculo acima identificado.\n" .
    "Guarde este documento para sua conferência."
);

$pdf->Ln(10);
$pdf->Cell(0, 5, 'Assinatura: ________________________________', 0, 1, 'L');

// Saída do PDF
$nomeArquivo = 'recibo_' . $placa . '_' . $id . '.pdf';
$pdf->Output('I', $nomeArquivo);
