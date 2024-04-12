<?php
// src/Service/ProfanityFilter.php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ProfanityFilter
{
    private $forbiddenWords;

    public function __construct(array $forbiddenWords)
    {
        $this->forbiddenWords = $forbiddenWords;
    }

    public function containsProfanity(string $text): bool
    {
        foreach ($this->forbiddenWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}