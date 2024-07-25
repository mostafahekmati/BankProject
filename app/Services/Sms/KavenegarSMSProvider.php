<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class KavenegarSMSProvider implements SmsInterface
{
    protected $api;


    const SUCCESS_STATUS = 1;
    const SENDER_ID = '12345';

    public function __construct()
    {
        $this->api = new KavenegarApi(env('KAVENEGAR_API_KEY'));
    }

    public function sendSMS(string $to, string $message): bool
    {
        try {

            $response = $this->api->Send(self::SENDER_ID, $to, $message);

            return $response[0]?->status === self::SUCCESS_STATUS;
        } catch (ApiException|HttpException $e) {
            Log::error('Kavenegar API Exception: ' . $e->getMessage());
            return false;
        }
    }
}
