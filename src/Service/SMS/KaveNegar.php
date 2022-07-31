<?php

namespace App\Service\SMS;



use App\Interfaces\SMSInterface;

class KaveNegar implements SMSInterface
{
    public string $apiKey;
    public string|array $mobile;
    public string $message;
    public string $endpoint;

    public function __construct(string|array $mobile, $message)
    {
        $this->apiKey = $_ENV['KAVEHNEGAR_API_KEY']?: '';
        $this->mobile = $mobile;
        $this->message = $message;
        $this->endpoint = str_ireplace('{API-KEY}', $this->apiKey, 'https://api.kavenegar.com/v1/{API-KEY}/sms/send.json');
    }

    public function send(): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        if (is_array($this->mobile)) {
            $receptor = implode(',', $this->mobile);
        } else {
            $receptor = $this->mobile;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query([
                'receptor' => $receptor,
                'message' => $this->message
            ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        return ($server_output == "OK");
    }
}
