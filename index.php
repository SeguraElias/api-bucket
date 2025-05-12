<?php
require_once __DIR__ . '/src/config/s3config.php';
require_once __DIR__ . '/src/controllers/imageController.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$imageController = new imageController();

$basePath = '/images';
$endpoint = str_replace($basePath, '', $requestUri);
$endpoint = rtrim($endpoint, '/');

header('Content-Type: application/json');

if ($requestMethod == 'POST' && strpos($requestUri, '/upload') !== false) {
    echo json_encode($imageController->uploadImage());
    exit;
} else {
    http_response_code(404);
    $response = [
        'success' => false,
        'error' => 'Endpoint no encontrado',
        'uri' => $requestUri,
        'method' => $requestMethod,
        'endpoint' => $endpoint
    ];
    echo json_encode($response);
}