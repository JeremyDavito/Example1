<?php

namespace App\Service;

class ApiImageUploader
{
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * Upload a base 64 encoded image in the folder of your choice in public/uploads
     *
     * @param string $originalName
     * @param string $imageValue
     * @param string $subDirectory
     * @return string
     */
    public function uploadBase64Image(string $originalName, string $imageValue, string $subDirectory): string
    {
        $base64Content = UploadedBase64File::extractBase64String($imageValue);
        $imageFile = new UploadedBase64File($base64Content, $originalName);

        return $this->fileUploader->upload($imageFile, $subDirectory);
    }
}