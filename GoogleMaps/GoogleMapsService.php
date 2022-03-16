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
                        Log::channel("api_log")->info($response->json());
                        $location = $results[0]["geometry"]["location"];
                        return new GoogleMapsLocationModel($location["lat"], $location["lng"]);
                }  
            }

            return new GoogleMapsLocationModel(0 , 0);
        }catch(Throwable $e){
            Log::channel("api_log")->error($response->json());
            Log::channel("api_log")->error($e->getMessage());
            return new GoogleMapsLocationModel(0 , 0);
        }
    }

    private function FetchData($address){

        return Http::get(GoogleMapsApiConfiguration::ApiUrl(),[
            'address' => $address,
            'key' => GoogleMapsApiConfiguration::ApiKey(),
        ]);
    }
}