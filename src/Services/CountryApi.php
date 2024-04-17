<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CountryApi
{
    private $client;
    private $apiUrl;

    public function __construct(HttpClientInterface $client, string $apiUrl)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
    }

    public function getCountries(): array
    {
        $response = $this->client->request('GET', $this->apiUrl);
        $data = $response->toArray();

        // Extract country names
        $countries = [];
        foreach ($data as $country) {
            $countries[] = $country['name']['common'];
        }

        return $countries;
    }
}


