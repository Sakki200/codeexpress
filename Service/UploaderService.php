<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 Service d'upload de fichier dans l'app CodeXpress
 - Image (.jpg, .jpeg, .png, .gif)
 - Documents (LATER)

 Méthodes : Upload, Delete
 */

class UploaderService
{

    private $param;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }

    public function uploadImage($file): string
    {
        try {
            //SI ON VEUT GARDER LE NOM ORIGINAL
            // $originalName = pathinfo($file->getClientOrigninalName(), PATHINFO_EXTENSION);

            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->param->get('uploads_images_directory'), $fileName);
            return $this->param->get('uploads_images_directory') . '/' . $fileName;
        } catch (\Exception $e) {
            throw new \Exception('Error occured while uploading the image: ');
        }
    }
}
