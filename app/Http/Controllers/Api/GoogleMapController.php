<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GoogleMaps\GoogleMapsApiConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleMapController extends Controller
{
    public function Get($latlng){
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latlng."&key=".GoogleMapsApiConfiguration::ApiKey();
        
        $host = request()->getHost();
        if($host == "services.kiswaksa.com"){
            $response = Http::withoutVerifying()
            ->withOptions(["verify"=>false])->get($url);
            if($response->ok()){
                $response->json();
            }else{
                Log::channel("errorlog")->error($response->json());
            }
        }

        return json_encode([
            "error_message" => "API keys with referer restrictions cannot be used with this API.",
            "results" => ["host" => $host,],
            "status" => "REQUEST_DENIED",
        ]);
    }
}
