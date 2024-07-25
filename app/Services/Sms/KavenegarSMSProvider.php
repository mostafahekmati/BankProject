<?php

namespace App\Services\Sms;

use App\Constants\SmsConstants;
use Illuminate\Support\Facades\Log;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class KavenegarSMSProvider implements SmsInterface
{
    protected $api;


    public function __construct()
    {
        $this->api = new KavenegarApi(env('KAVENEGAR_API_KEY'));
    }

    public function sendSMS(string $to, string $message): bool
    {
        try {

            $response = $this->api->Send(SmsConstants::KAVENEGAR_SENDER_ID, $to, $message);

            return $response[0]?->status === SmsConstants::KAVENEGAR_SUCCESS_STATUS;
        } catch (ApiException|HttpException $e) {
            Log::error('Kavenegar API Exception: ' . $e->getMessage());
            return false;
        }
    }
}
