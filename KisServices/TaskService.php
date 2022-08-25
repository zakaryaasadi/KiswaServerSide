<?php

namespace KisServices;

use App\Models\ReviewModel;
use GoogleMaps\GoogleMapsLocationModel;
use GoogleMaps\GoogleMapsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KisCore\Infrastructure\Singleton;
use KisData\StatusCode;
use KisData\ResponseModel;
use Tookan\DefaultValues\TookanCountries;
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
    

    public function SendReviewMessageToSuccessTasks($country){
        $response = $this->tookanTaskService->SuccessTasks(TookanCountries::$Values[$country]["TEAM_ID"]);

        if(!$response->ok()){
            Log::channel('auto_review_log')->info($response->json());
            return; 
        }

        $data = $response->object()->data;
        $total_page_count = $response->object()->total_page_count;

        for($page = 2; $page <= $total_page_count; $page++){
            $response = $this->tookanTaskService->SuccessTasks(TookanCountries::$Values[$country]["TEAM_ID"], $page);
            $data = array_merge($data, $response->object()->data);
        }

        foreach($data as $item){
            $item->country = $country;
            $item->job_pickup_phone = str_replace(" ","",$item->job_pickup_phone);

            $review = ReviewModel::where("job_id", $item->job_id)->first();
            if($review == null){
                // Send message to client on Whatsapp
               $this->sendWhatsappMessage($item->job_id, $item->job_pickup_name, $item->job_pickup_phone, $item->fleet_name, $country);

                // Save the job in database
                ReviewModel::Create((array)$item);
            }
        }

        Log::channel('auto_review_log')->info(count($data) . " total of records");
        Log::channel('auto_review_log')->info($data);        
    }

#


#Uilities

    private function GetLocationByAddress($address){
        $location = new GoogleMapsLocationModel(0, 0);
            if (filter_var($address, FILTER_VALIDATE_URL) && count(explode('=', $address)) == 2) {
                $axis = explode('=', $address)[1];
                $point = explode(',', $axis);
                $location = new GoogleMapsLocationModel($point[0], $point[1]);
            }else{
                $location = $this->googleMapsService->GetLocationByAddress($address);
            }

        return $location;
    }

    private function sendWhatsappMessage($jobId, $name, $phone, $fleetName, $country){
        $body = [
            "job_id" => $jobId,
            "name" => $name,
            "phone" => $phone,
            "fleet_name" => $fleetName,
        ];

        Http::withoutVerifying()
        ->withOptions(["verify"=>false])
                ->post(TookanCountries::$Values[$country]["MESSAGE_BIRD_SURVEY"], $body);
    }

#

}