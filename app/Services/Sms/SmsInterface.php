<?php

namespace App\Services\Sms;

interface SmsInterface
{
    public function sendSMS(string $to, string $message): bool;
}
