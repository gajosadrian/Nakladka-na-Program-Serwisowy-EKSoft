<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Sms;

class HostedSms {
    private $client;
    private $url = 'https://api.hostedsms.pl';
    private $login;
    private $password;
    private $sender = 'Serwis AGD';

    public function __construct()
    {
        $this->login = config('app.hostedsms_login');
        $this->password = config('app.hostedsms_password');

        $this->client = new Client([
            'base_uri' => $this->url,
        ]);
    }

    public function send(array $phones, string $message): ?string
    {
        $responses = [];

        foreach ($phones as $phone) {
            $responses[] = $this->client->post('SimpleApi', [
                'json' => [
                    'UserEmail' => $this->login,
                    'Password' => $this->password,
                    'Sender' => $this->sender,
                    'Phone' => $phone,
                    'Message' => $message,
                ],
            ]);
        }

        $body = json_decode($responses[0]->getBody(), true);

        if (! isset($body['MessageId'])) return null;

        $sms = new Sms;
        $sms->user_id = auth()->user()->id;
        $sms->type = 'sent';
        $sms->sender = $this->sender;
        $sms->phones = $phones;
        $sms->message = $message;
        $sms->save();

        return $body['MessageId'];
    }
}