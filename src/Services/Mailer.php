<?php
// src/Services/Mailer.php
namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($recipientEmail)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($recipientEmail)
            ->subject('Warning: Inappropriate Language in Your Comment')
            ->text('Hello, Your comment contains inappropriate language. Please refrain from using such language in the future.');

        $this->mailer->send($email);
    }
}