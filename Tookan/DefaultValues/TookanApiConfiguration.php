<?php

namespace Tookan\DefaultValues;


class TookanApiConfiguration{
    const UserId = "90162";


    public static function ApiUrl(){
        return env("TOOKAN_API_URL");
    }

    public static function ApiKey(){
        return env("TOOKAN_API_KEY");
    }
}