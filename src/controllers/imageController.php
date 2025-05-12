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

            if ($_ENV['AWS_BUCKET_VISIBILITY'] === 'private') {
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $_ENV['AWS_BUCKET_NAME'],
                'Key' => $filename
            ]);

            $request = $this->s3Client->createPresignedRequest($cmd, '+1 hour');
            $imageUrl = (string)$request->getUri();
            } else {
                // Para buckets públicos
                $imageUrl = $result->get('ObjectURL');
            }
    
            return [
                'success' => true,
                'url' => $imageUrl,
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
