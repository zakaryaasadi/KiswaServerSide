<?php

namespace KisServices;

use GoogleMaps\GoogleMapsLocationModel;
use GoogleMaps\GoogleMapsService;
use KisCore\Infrastructure\Singleton;
use KisData\StatusCode;
use KisData\ResponseModel;
use Tookan\Services\TookanTaskService;

class TaskService{

#Fields

    private $autoAsignService;
    private $googleMapsService;
    private $tookanTaskService;

#


#Ctor

    public function __construct()
    {
        $this->tookanTaskService = Singleton::Create(TookanTaskService::class);
        $this->googleMapsService = Singleton::Create(GoogleMapsService::class);
        $this->autoAsignService = Singleton::Create(AutoAsignQueueService::class);
    }


#


# Method


    public function GetAssignTaskByCustomerPhone($customerPhone){
        $tasks = $this->tookanTaskService->GetAssignTaskByOrderId($customerPhone);
        if(Count($tasks) > 0){
            return new ResponseModel(StatusCode::Success , "Successfull", [
                "task_id" => $tasks[0]->job_id,
                "datetime" => date("Y-m-d", strtotime($tasks[0]->job_pickup_datetime)),
            ]);
        }

        return new ResponseModel(StatusCode::SuccessBut , "No previous request"); 
    }

    public function CreateTask($customerName, $customerPhone, $createdBy, $country, $address, $template = "", $customFields = []){

        $tasks = $this->tookanTaskService->GetTasksNotCompletedByOrderId($customerPhone);
        if(Count($tasks) > 0){
            return new ResponseModel(StatusCode::Pending , "You cannot create a new task because there is still a pending task", [
                "task_id" => $tasks[0]->job_id,
                "datetime" => date("Y-m-d", strtotime($tasks[0]->job_pickup_datetime)),
            ]);
        }

        $country = strtoupper($country);
        $location = $this->GetLocationByAddress($address);
        $response = $this->tookanTaskService->CreateTask($customerName, 
                                    $customerPhone, 
                                    $createdBy, 
                                    $country, 
                                    $address, 
                                    $location->lat,
                                    $location->lng,
                                    $template,
                                    $customFields);


                                
        if($response->ok()){

            if($response->object()->status == 200){
                $this->autoAsignService->AddToQueue($response->object()->data, $country);
            }

            return new ResponseModel($response->object()->status, $response->object()->message, $response->object()->data);

        }else{
            return new ResponseModel($response->status(), "There was an error creating the request");
        }
    }
    


#


#Uilities

    private function GetLocationByAddress($address){
        $location = new GoogleMapsLocationModel(0, 0);
            if (filter_var($address, FILTER_VALIDATE_URL) && count(explode('=', $address)) > 1) {
                $axis = explode('=', $address)[1];
                $point = explode(',', $axis);
                $location = new GoogleMapsLocationModel($point[0], $point[1]);
            }else{
                $location = $this->googleMapsService->GetLocationByAddress($address);
            }

        return $location;
    }

#

}