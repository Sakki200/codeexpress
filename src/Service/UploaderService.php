<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderService
{
    private $param;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }

    public function uploadImage(UploadedFile $file): string
    {
        try {
            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->param->get('uploads_images_directory'), $fileName);

            return $fileName;
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while uploading the image: ' . $e->getMessage());
        }
    }

    public function deleteImage(string $fileName): void
    {
        try {
            $filePath = $this->param->get('uploads_images_directory') . '/' . $fileName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while deleting the image: ' . $e->getMessage());
        }
    }
}
