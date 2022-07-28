<?php

namespace GoogleMaps;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GoogleMapsService{
    public function GetLocationByAddress( $address){
        $response = $this->FetchData($address);
        try{
            if($response->ok()){
                    $results = $response->json()["results"];
                    if(Count($results) > 0){
                        $location = $results[0]["geometry"]["location"];
                        return new GoogleMapsLocationModel($location["lat"], $location["lng"]);
                }  
            }

            return $this->InitLocation($response);
        }catch(Throwable $e){
            Log::channel("errorlog")->error($e->getMessage());
            return $this->InitLocation($response);
        }
    }

    private function FetchData($address){

        return Http::get(GoogleMapsApiConfiguration::ApiUrl(),[
            'address' => $address,
            'key' => GoogleMapsApiConfiguration::ApiKey(),
        ]);
    }

    private function InitLocation($response){
            Log::channel("errorlog")->error($response->json());
            return new GoogleMapsLocationModel(0 , 0);
    }
}