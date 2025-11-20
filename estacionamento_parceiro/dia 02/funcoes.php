<?php
// ======================================
// funcoes.php – Funções auxiliares gerais
// ======================================

// --------------------------------------
// PLACA
// --------------------------------------

/**
 * Normaliza a placa:
 *  - remove espaços e traços
 *  - converte para maiúsculo
 */
function normalizar_placa(string $placa): string
{
    return strtoupper(str_replace(['-', ' '], '', trim($placa)));
}

/**
 * Valida placa no padrão brasileiro:
 *  - Antigo: ABC1234
 *  - Mercosul: ABC1D23
 */
function validar_placa(string $placa): bool
{
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

// --------------------------------------
// TEMPO / DIÁRIAS
// --------------------------------------

/**
 * Retorna diferença bruta entre entrada e saída
 * em dias / horas / minutos (array simples).
 */
function calcular_tempo(string $entrada, string $saida): array
{
    $dtEntrada = new DateTime($entrada);
    $dtSaida   = new DateTime($saida);
    $diff      = $dtEntrada->diff($dtSaida); // DateInterval

    return [
        'dias'    => $diff->days,
        'horas'   => $diff->h,
        'minutos' => $diff->i,
    ];
}

/**
 * Retorna tempo total em formato legível (ex: "1 dia 3h 20min").
 */
function calcular_tempo_total(string $entrada, string $saida): string
{
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

/**
 * Calcula número de diárias:
 *  - cada 24h OU FRAÇÃO conta 1 diária
 *  - mínimo de 1 diária
 */
function calcular_num_diarias(string $entrada, string $saida): int
{
    $dtEntrada = new DateTime($entrada);
    $dtSaida   = new DateTime($saida);
    $diff      = $dtEntrada->diff($dtSaida);

    // total em horas (se tiver minuto > 0, arredonda pra +1h)
    $horas = ($diff->days * 24) + $diff->h + ($diff->i > 0 ? 1 : 0);

    if ($horas <= 0) {
        return 1;
    }

    $diarias = (int)ceil($horas / 24);

    return max($diarias, 1);
}

// --------------------------------------
// PREÇOS – usando tabela_precos (configurável)
// --------------------------------------

/**
 * Busca a REGRA de preço na tabela_precos
 * com base em:
 *  - tipo_veiculo (CARRO / ONIBUS)
 *  - cliente_tipo (NORMAL / PARCEIRO)
 *  - numDiarias (usa faixa min_dias / max_dias)
 *
 * Retorna array com os campos da regra ou null.
 */
function obter_regra_preco(
    PDO $pdo,
    string $tipoVeiculo,
    string $clienteTipo,
    int $numDiarias
): ?array {
    $sql = "
        SELECT tipo_veiculo, cliente_tipo, min_dias, max_dias, valor_diaria
        FROM tabela_precos
        WHERE tipo_veiculo = :tipo
          AND cliente_tipo = :cliente
          AND :dias >= min_dias
          AND (:dias <= max_dias OR max_dias IS NULL)
        ORDER BY min_dias DESC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':tipo'    => strtoupper($tipoVeiculo),
        ':cliente' => strtoupper($clienteTipo),
        ':dias'    => $numDiarias,
    ]);

    $regra = $stmt->fetch(PDO::FETCH_ASSOC);

    return $regra ?: null;
}

/**
 * Calcula o VALOR TOTAL a cobrar, usando tabela_precos.
 *
 * OBS: o nome original da função é mantido porque já é usado
 * em saida_buscar.php e saida_finalizar.php.
 *
 * Fluxo:
 *  1) descobre número de diárias (fora desta função)
 *  2) encontra a regra que cobre esse intervalo de dias
 *  3) pega valor_diaria
 *  4) total = valor_diaria * numDiarias
 */
function calcular_valor_por_diaria(
    PDO $pdo,
    string $tipoVeiculo,
    string $clienteTipo,
    int $numDiarias
): float {
    $numDiarias = max(1, (int)$numDiarias);

    $regra = obter_regra_preco($pdo, $tipoVeiculo, $clienteTipo, $numDiarias);

    if (!$regra) {
        // Se não achar regra, retorna 0.00 para evitar quebrar o sistema.
        // Você pode trocar isso por throw Exception se quiser ser mais rígido.
        return 0.00;
    }

    $valorDiaria = (float)$regra['valor_diaria'];
    $total       = $valorDiaria * $numDiarias;

    return round($total, 2);
}
