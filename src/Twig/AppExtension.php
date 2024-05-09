<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('time_ago', [$this, 'calculateTimeAgo']),
        ];
    }

    public function calculateTimeAgo(\DateTimeInterface $dateTime)
    {
        $currentDateTime = new \DateTime();
        $interval = $currentDateTime->diff($dateTime);
        
        if ($interval->y > 0) {
            return $interval->format('%y years ago');
        } elseif ($interval->m > 0) {
            return $interval->format('%m months ago');
        } elseif ($interval->d > 0) {
            return $interval->format('%d days ago');
        } elseif ($interval->h > 0) {
            return $interval->format('%h hours ago');
        } elseif ($interval->i > 0) {
            return $interval->format('%i minutes ago');
        } else {
            return 'just now';
        }
    }
}