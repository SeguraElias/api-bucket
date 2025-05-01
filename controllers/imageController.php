<?php

require_once __DIR__ . '/../config/s3config.php';

class imageController {
    private $s3Client;
    private $allowedTypes;
    private $maxFileSize;

    public function __construct() {
        $this->s3Client = getS3Bucket();
    }

    public function uploadImage() {
        try {
            if (!isset($_FILES['image'])) {
                throw new Exception("No se recibió ninguna imagen");
            }
    
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir el archivo: " . $file['error']);
            }
    
            $filename = uniqid() . '_' . basename($file['name']);
    
            $result = $this->s3Client->putObject([
                'Bucket' => $_ENV['AWS_BUCKET_NAME'],
                'Key' => $filename,
                'SourceFile' => $file['tmp_name'],
                'ContentType' => $file['type']
            ]);
    
            return [
                'success' => true,
                'url' => $result['ObjectURL'],
                'fileName' => $filename
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
