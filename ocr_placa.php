<?php
// Somente desenvolvimento – depois pode desligar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método inválido']);
    exit;
}

if (empty($_FILES['foto_placa']['tmp_name'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nenhuma imagem enviada']);
    exit;
}

// === CONFIGURE SUA API AQUI ===
$apiKey = 'SUA_API_KEY_DO_PLATE_RECOGNIZER';

// Endpoint oficial Snapshot API
$url = 'https://api.platerecognizer.com/v1/plate-reader/';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Token ' . $apiKey
    ],
    CURLOPT_POSTFIELDS => [
        // 'regions' => 'br', // opcional: restringir ao Brasil
        'upload' => new CURLFile($_FILES['foto_placa']['tmp_name'])
    ]
]);

$resposta = curl_exec($ch);

if ($resposta === false) {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao chamar serviço de reconhecimento.']);
    exit;
}

curl_close($ch);

$dados = json_decode($resposta, true);

$placa = '';
if (!empty($dados['results'][0]['plate'])) {
    // API costuma devolver sem formatação, ex: abc1d23
    $placa = strtoupper($dados['results'][0]['plate']);
}

// Resposta para o JavaScript
if ($placa) {
    echo json_encode(['placa' => $placa]);
} else {
    echo json_encode(['erro' => 'Nenhuma placa encontrada na imagem.']);
}
