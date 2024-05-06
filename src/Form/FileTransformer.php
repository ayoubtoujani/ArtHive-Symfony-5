<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class FileTransformer implements DataTransformerInterface
{
    public function transform($file)
    {
        // Transform the file object to a string (e.g., file path)
        if ($file instanceof File) {
            return $file->getPathname();
        }

        return null;
    }

    public function reverseTransform($string)
    {
        // Transform the string (e.g., file path) to a file object
        if ($string) {
            return new File($string);
        }

        return null;
    }
}