<?php

namespace GoogleMaps;

use Illuminate\Support\Facades\Http;

class GoogleMapsService{
    public function GetLocationByAddress( $address){
        $response = $this->FetchData($address);
        if($response->ok()){
            if(!isset($response->json()['results'])){
                $results = $response->json()["results"];
                if(Count($results) > 0){
                    $location = $results[0]["geometry"]["location"];
                    return new GoogleMapsLocationModel($location["lat"], $location["lng"]);
                }  
            }  
        }

        return new GoogleMapsLocationModel(0 , 0);
    }

    private function FetchData($address){

        return Http::get(GoogleMapsApiConfiguration::ApiUrl(),[
            'address' => $address,
            'key' => GoogleMapsApiConfiguration::ApiKey(),
        ]);
    }
}