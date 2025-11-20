<?php
// ===============================
// funcoes.php – Auxiliares gerais
// ===============================

// Normaliza placa: remove traços, espaços e deixa maiúscula
function normalizar_placa(string $placa): string {
    return strtoupper(str_replace(['-', ' '], '', trim($placa)));
}

// Valida placa formato Brasil (antigo e Mercosul)
function validar_placa(string $placa): bool {
    $placa = normalizar_placa($placa);

    // Formato antigo: ABC1234
    if (preg_match('/^[A-Z]{3}[0-9]{4}$/', $placa)) {
        return true;
    }

    // Mercosul: ABC1D23
    if (preg_match('/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/', $placa)) {
        return true;
    }

    return false;
}

// Calcula tempo “bruto” entre entrada e saída
function calcular_tempo(string $entrada, string $saida): array {
    $dtEntrada = new DateTime($entrada);
    $dtSaida   = new DateTime($saida);
    $diff      = $dtEntrada->diff($dtSaida);

    return [
        'dias'    => $diff->days,
        'horas'   => $diff->h,
        'minutos' => $diff->i,
    ];
}

// Retorna tempo total em formato legível (ex: "1 dia 3h 20min")
function calcular_tempo_total(string $entrada, string $saida): string {
    $dtEntrada = new DateTime($entrada);
    $dtSaida   = new DateTime($saida);
    $intervalo = $dtEntrada->diff($dtSaida);

    $dias  = $intervalo->days;
    $horas = $intervalo->h;
    $min   = $intervalo->i;

    $partes = [];

    if ($dias > 0) {
        $partes[] = $dias . ' dia' . ($dias > 1 ? 's' : '');
    }
    if ($horas > 0) {
        $partes[] = $horas . 'h';
    }
    if ($min > 0) {
        $partes[] = $min . 'min';
    }

    if (empty($partes)) {
        return '0min';
    }

    return implode(' ', $partes);
}

// Calcula número de diárias: cada 24h (ou fração) conta 1 diária
function calcular_num_diarias(string $entrada, string $saida): int {
    $dtEntrada = new DateTime($entrada);
    $dtSaida   = new DateTime($saida);
    $diff      = $dtEntrada->diff($dtSaida);

    // total em horas (arredondando minuto > 0 para +1h)
    $horas = ($diff->days * 24) + $diff->h + ($diff->i > 0 ? 1 : 0);

    if ($horas <= 0) {
        return 1;
    }

    $diarias = (int)ceil($horas / 24);

    return max($diarias, 1);
}

// Busca valor da diária no banco pela categoria
function buscar_valor_diaria(PDO $pdo, string $categoria): float {
    $sql  = "SELECT valor_diaria FROM regras_precos WHERE categoria = :cat LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':cat' => $categoria]);
    $row = $stmt->fetch();

    if (!$row) {
        return 0.00;
    }

    return (float)$row['valor_diaria'];
}

// Calcula o valor total com base na categoria e número de diárias
function calcular_valor_total(PDO $pdo, string $categoria, int $numDiarias): float {
    $valorDiaria = buscar_valor_diaria($pdo, $categoria);

    if ($valorDiaria <= 0) {
        return 0.00;
    }

    $total = $valorDiaria * $numDiarias;

    return round($total, 2);
}

// Converte tipo de veículo + tipo de cliente + nº de diárias em categoria de preço
function calcular_valor_por_diaria(
    PDO $pdo,
    string $tipoVeiculo,
    string $clienteTipo,
    int $numDiarias
): float {
    $tipoVeiculo = strtoupper($tipoVeiculo);
    $clienteTipo = strtoupper($clienteTipo);

    // Mapeamento das regras:
    // CARRO → sempre categoria CARRO
    // ÔNIBUS NORMAL:
    //   1 diária      → ONIBUS_1
    //   2+ diárias    → ONIBUS_3P
    // ÔNIBUS MAZE:
    //   1 diária      → MAZE_1
    //   2+ diárias    → MAZE_3P

    if ($tipoVeiculo === 'CARRO') {
        $categoria = 'CARRO';
    } elseif ($tipoVeiculo === 'ONIBUS' && $clienteTipo === 'NORMAL') {
        if ($numDiarias <= 1) {
            $categoria = 'ONIBUS_1';
        } else {
            $categoria = 'ONIBUS_3P';
        }
    } elseif ($tipoVeiculo === 'ONIBUS' && $clienteTipo === 'PARCEIRO') {
    if ($numDiarias <= 1) {
        $categoria = 'PARCEIRO_1';
    } else {
        $categoria = 'PARCEIRO_3P';
    }
}
    } else {
        // fallback – se vier algo estranho, considera CARRO
        $categoria = 'CARRO';
    }

    return calcular_valor_total($pdo, $categoria, $numDiarias);
}
?>
