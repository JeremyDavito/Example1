<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedBase64File extends UploadedFile
{
    /*
     * Create a temporary image based on a base64 encode string
     */
    public function __construct(string $base64String, string $originalName)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
        $data = base64_decode($base64String);
        file_put_contents($filePath, $data);

        parent::__construct($filePath, $originalName, null, null, true);
    }

    /*
     * Extract the base64 string
     */
    static public function extractBase64String(string $base64Content): string
    {
        $data = explode(';base64', $base64Content);
        return $data[1];
    }
}