<?php

namespace Tookan\Services;

use KisCore\Infrastructure\Singleton;
use Tookan\DefaultValues\TookanCountries;
use Tookan\DefaultValues\TookanTaskStatus;
use Tookan\Http\TaskApi;

class TookanTaskService{

#region Fields

   private $taskApi;

#endregion



#region Ctor

    public function __construct()
    {
        $this->taskApi = Singleton::Create(TaskApi::class);
    }

#endregion


#region Methods

    public function GetTasksByAgentIds($agentIds, $day){
        $arr = [];
        foreach($agentIds as $agentId){
            $arr[$agentId] = $this->GetTasksByAgentId($agentId, $day);
        }

        return $arr;
    }

    public function GetAssignTaskByOrderId($orderId){
        $body = [
            "order_id" => [$orderId],
            "job_status" => [TookanTaskStatus::Assigned, 
                            TookanTaskStatus::AcceptedOrAcknowledged, TookanTaskStatus::Started,
                            TookanTaskStatus::InProgressOrArrived],           
        ];

        $response = $this->taskApi->GetTasks($body);
        if($response->ok() && $response->object()->status){
            return $response->object()->data;
        }
        return [];
    }


    public function GetTasksNotCompletedByOrderId($orderId){
        $body = [
            "order_id" => [$orderId],
            "job_status" => [TookanTaskStatus::Unassigned, TookanTaskStatus::Assigned, 
                            TookanTaskStatus::AcceptedOrAcknowledged, TookanTaskStatus::Started,
                            TookanTaskStatus::InProgressOrArrived],           
        ];

        $response = $this->taskApi->GetTasks($body);
        if($response->ok() && $response->object()->status){
            return $response->object()->data;
        }
        return [];
    }


    public function CreateTask($customerName, $customerPhone, $createdBy, $country, $address, $latitude, $longitude, $template, $customFields){

        date_default_timezone_set(TookanCountries::$Values[$country]["TIMEZONE_CODE"]);
        $datetime = date("Y-m-d H:i:s");

        $body = [
            "order_id" => $customerPhone,
            "job_pickup_phone" => $customerPhone,
            "job_pickup_name" => $customerName,
            "job_pickup_address" => $address,
            "job_pickup_latitude" => $latitude,
            "job_pickup_longitude" => $longitude,
            "job_pickup_datetime" => $datetime,
            "job_description" => $createdBy . ' ' . TookanCountries::$Values[$country]["ALPHA_CODE"],
            "team_id" => TookanCountries::$Values[$country]["TEAM_ID"],
            "timezone" => TookanCountries::$Values[$country]["TIMEZONE"],
            "pickup_custom_field_template" => $template,
            "pickup_meta_data" => $customFields,
        ];

        return $this->taskApi->CreateTask($body);
    }

    public function AssignTaskToAgent($job, $fleetId){
        return $this->taskApi->AssignTaskToAgent($job->job_id, $fleetId, TookanCountries::$Values[$job->country]["TEAM_ID"],);
    }

    public function UpdateTaskDate($jobId, $date){
        return $this->taskApi->UpdateTaskDate($jobId, $date);
    }


#endregion


#region Utilities

    private function GetTasksByAgentId($agentId, $day){

        $date = date("Y-m-d", $day);

        $body = array(
            "job_status" => [TookanTaskStatus::Assigned, TookanTaskStatus::InProgressOrArrived, 
                            TookanTaskStatus::AcceptedOrAcknowledged, TookanTaskStatus::Started,
                            TookanTaskStatus::Failed, TookanTaskStatus::Cancel, 
                            TookanTaskStatus::Decline, TookanTaskStatus::Successful],
            "start_date" => $date,
            "end_date" => $date,
            "fleet_id" => $agentId
        );

        $res = $this->taskApi->GetTasks($body);
        if($res->ok()){
            $data = $res->json()['data'];
            return $data;
        }
        return [];
    }

#endregion


}