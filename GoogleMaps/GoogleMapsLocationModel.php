<?php

namespace GoogleMaps;

class GoogleMapsLocationModel{
    public $lat = 0;
    public $lng = 0;

    public function __construct($lat, $lng)
    {
        if(is_numeric($lat) && is_numeric($lng)){
            $this->lat = $lat;
            $this->lng = $lng;
        }
    }
}