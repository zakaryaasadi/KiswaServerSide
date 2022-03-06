<?php

namespace GoogleMaps;


class GoogleMapsApiConfiguration{

    public static function ApiUrl(){
        return env("GOOGLE_API_URL");
    }

    public static function ApiKey(){
        return env("GOOGLE_API_KEY");
    }
}