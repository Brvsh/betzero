<?php

$client_id = 'melissagh_5095596839';
$client_secret = '192b30e5830c43cee079cca2e31ebb2536bd0c3a071e8e9293f24737f3f7c177';
$base_url = 'https://api.pixup.com.br';

$token_response = file_get_contents($base_url . '/oauth/token', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
        ])
    ]
]));

$token_data = json_decode($token_response, true);
if (!isset($token_data['access_token'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gerar token']);
    exit;
}

$token = $token_data['access_token'];

$data = json_decode(file_get_contents('php://input'), true);
$valor = $data['amount'] ?? null;
$id = uniqid();

$qrcode_response = file_get_contents($base_url . '/v1/pix/qrcode', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
        'content' => json_encode([
            'valor' => $valor,
            'idExterno' => $id,
            'descricao' => 'Depósito via intermediador',
            'callback' => $data['callback'] ?? null
        ])
    ]
]));

echo $qrcode_response;
?>
