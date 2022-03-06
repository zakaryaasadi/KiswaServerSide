<?php

namespace KisData;

class ResponseModel{
    public $message;
    public $status;
    public $results;


    public function __construct($status, $message, $results = [])
    {
        $this->message = $message;
        $this->status = $status;
        $this->results = $results;
    }

    public function toJson(){
        return json_encode($this);
    }
}