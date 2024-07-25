<?php

namespace App\Services\Sms;

class GhasedakSMSProvider implements SmsInterface
{


    public function sendSMS(string $to, string $message): bool
    {
        $sender = 123455;
        $curl = curl_init();



        curl_setopt_array($curl, array(
            CURLOPT_URL => env('CURLOPT_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query(array(
                'message' => $message,
                'sender' => $sender,
                'Receptor' => $to
            )),
            CURLOPT_HTTPHEADER => array(
                "apikey: your_apikey",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
        return true;
    }
}

