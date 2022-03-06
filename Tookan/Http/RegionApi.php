<?php

namespace Tookan\Http;
use Tookan\DefaultValues\TookanApiConfiguration;


class RegionApi{

    public function GetRegionById($id){

        $body = array(
            "region_id" => $id,
            "user_id" => TookanApiConfiguration::UserId
        );

        return TookanApi::Callback("view_regions", $body);
    }
}