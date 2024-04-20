<?php
// BadWordsService.php
namespace App\Services;

class BadWordsService
{
    private $badWords;

    public function __construct(string $badWordsFilePath)
    {
        $this->badWords = json_decode(file_get_contents($badWordsFilePath), true)['bad_words'];
    }

    public function containsBadWord(string $comment): bool
    {
        foreach ($this->badWords as $badWord) {
            if (stripos($comment, $badWord) !== false) {
                return true;
            }
        }
        return false;
    }
}
