<?php
// src/Service/MailgunEmailService.php
namespace App\Services;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailgunEmailService
{
    private $mailer;
    private $domain;

    public function __construct(MailerInterface $mailer, string $domain)
    {
        $this->mailer = $mailer;
        $this->domain = $domain;
    }

    public function sendEmail(string $from, string $to, string $subject, string $text): void
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($text);

        $this->mailer->send($email);
    }
}