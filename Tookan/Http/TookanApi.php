<?php

namespace Tookan\Http;
use Illuminate\Support\Facades\Http;
use Tookan\DefaultValues\TookanApiConfiguration;


class TookanApi{

    public static function Callback($methodName, $body){
        $body['api_key'] = TookanApiConfiguration::ApiKey();
        
        $res = Http::withoutVerifying()
                ->post(TookanApiConfiguration::ApiUrl()  . $methodName, $body);
        return $res;
    }
}