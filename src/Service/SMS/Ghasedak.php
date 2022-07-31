<?php

namespace App\Service\SMS;



use App\Interfaces\SMSInterface;

class Ghasedak implements  SMSInterface
{
    public string $apiKey;
    public string|array $mobile;
    public string $message;
    public string $endpoint;
    public function __construct(array|string $mobile, $message)
    {
        $this->apiKey = $_ENV['GHASEDAK_API_KEY'];
        $this->mobile = $mobile;
        $this->message = $message;
    }

    public function send():bool
    {
//        try{
//            if (is_array($this->mobile)) {
//                $receptor = implode(',', $this->mobile);
//            } else {
//                $receptor = $this->mobile;
//            }
//            $api = new \Ghasedak\GhasedakApi( $this->apiKey);
//            $api->SendSimple($receptor,$this->message);
//            return true;
//        }
//        catch(\Ghasedak\Exceptions\ApiException | \Ghasedak\Exceptions\HttpException $e){
//                return false;
//        }
    }
}
