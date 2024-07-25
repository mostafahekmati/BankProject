<?php

namespace App\Services\Sms;

class SMSService
{
    protected $provider;

    public function __construct(SMSInterface $provider)
    {
        $this->provider = $provider;
    }

    public function send(string $to, string $message): bool
    {
        return $this->provider->sendSMS($to, $message);
    }
}
