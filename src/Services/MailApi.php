<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailApi
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendPasswordEmail($email)
    {
        $response = $this->client->request('POST', 'http://localhost:3000/api/forgot-password', [
            'json' => ['email' => $email],
        ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Error sending password email');
        }
    }
}


