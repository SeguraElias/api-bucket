<?php
require_once __DIR__ . '/src/config/s3config.php';
require_once __DIR__ . '/src/controllers/imageController.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$imageController = new imageController();

$basePath = '/images';
$endpoint = str_replace($basePath, '', $requestUri);
$endpoint = rtrim($endpoint, '/');

if ($requestMethod == 'POST' && strpos($requestUri, '/upload') !== false) {
    echo json_encode($imageController->uploadImage());
    exit;
} 
elseif ($requestMethod == 'GET' && strpos($endpoint, '/get') !== false) {
    if (!isset($_GET['name'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Parámetro "name" requerido'
        ]);
        exit;
    }

    $result = $imageController->getImage($_GET['name']);
    http_response_code($result['success'] ? 200 : 404);
    echo json_encode($result);
    exit;
}
else {
    http_response_code(404);
    $response = [
        'success' => false, 
        'error' => 'Endpoint no encontrado',
        'uri' => $requestUri,
        'method' => $requestMethod,
        'endpoint' => $endpoint
    ];
}
echo json_encode($response);