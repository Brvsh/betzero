<?php

$client_id = 'melissagh_5095596839';
$client_secret = '192b30e5830c43cee079cca2e31ebb2536bd0c3a071e8e9293f24737f3f7c177';
$base_url = 'https://api.pixup.com.br';

// 1. GERAR TOKEN COM CURL
$ch = curl_init($base_url . '/oauth/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);
$token_response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro CURL token: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

$token_data = json_decode($token_response, true);
if (!isset($token_data['access_token'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gerar token']);
    exit;
}
$token = $token_data['access_token'];

// 2. LER DADOS DE ENTRADA
$data = json_decode(file_get_contents('php://input'), true);
$valor = $data['amount'] ?? null;
$id = uniqid();
$callback = $data['callback'] ?? null;

// 3. GERAR QRCODE COM CURL
$ch2 = curl_init($base_url . '/v1/pix/qrcode');
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'valor' => $valor,
        'idExterno' => $id,
        'descricao' => 'Depósito via intermediador',
        'callback' => $callback
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ],
]);

$qrcode_response = curl_exec($ch2);

if (curl_errno($ch2)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro CURL qrcode: ' . curl_error($ch2)]);
    exit;
}
curl_close($ch2);

// 4. RESPONDE COM QRCODE
echo $qrcode_response;
