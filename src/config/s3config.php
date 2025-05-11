<?php
require_once __DIR__ . '/../../vendor/autoload.php';

//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
}

$s3Config = [
    'version' => 'latest',
    'region' => $_ENV['AWS_REGION'],
    'credentials' => [
        'key' => $_ENV['AWS_ACCESS_KEY_ID'],
        'secret' => $_ENV['AWS_SECRET_ACCESS_KEY']
    ],
    'http' => [
        'verify' => false
    ]
];

$s3Client = new Aws\S3\S3Client($s3Config);

function getS3Bucket(){
    global $s3Client;
    return $s3Client;
}