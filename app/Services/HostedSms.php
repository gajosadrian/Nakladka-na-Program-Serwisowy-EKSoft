<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Sms;

class HostedSms {
    private $client;
    private $url = 'https://api.hostedsms.pl';
    private $login = 'biuro@dar-gaz.pl';
    private $password = 'Dargaz18K';
    private $sender = 'Serwis AGD';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
        ]);
    }

    public function send(int $phone, string $message): ?string
    {
        $response = $this->client->post('SimpleApi', [
            'json' => [
                'UserEmail' => $this->login,
                'Password' => $this->password,
                'Sender' => $this->sender,
                'Phone' => $phone,
                'Message' => $message,
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        if (! isset($body['MessageId'])) return null;

        $sms = new Sms;

        return $body['MessageId'];
    }
}