<?php
// src/Form/DataTransformer/ImageToFileTransformer.php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ImageToFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transforme l'objet File en chemin de fichier (chaîne de caractères)
        if ($value instanceof File) {
            return $value;
        }

        return null;
    }

    public function reverseTransform($value)
    {
        // Transforme le chemin de fichier (chaîne de caractères) en objet File
        if ($value) {
            return new File($value);
        }

        return null;
    }
}
