<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class KavenegarSMSProvider implements SmsInterface
{
    protected $api;

    public function __construct()
    {
        //api key is read from the .env file in Laravel.
        $this->api = new KavenegarApi(env('KAVENEGAR_API_KEY'));
    }

    public function sendSMS(string $to, string $message): bool
    {
        try {

            $sender = "12345" ;

            $response = $this->api->Send($sender, $to, $message);
            if (isset($response[0]->status) && $response[0]->status == 1) {
                return true;
            }
            Log::error('Kavenegar API error: ' . $response[0]->status);
            return false;
        } catch (ApiException|HttpException $e) {
            Log::error('Kavenegar API Exception: ' . $e->getMessage());
            return false;
        }
    }
}
