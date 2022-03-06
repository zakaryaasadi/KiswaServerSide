<?php

namespace KisServices;

use GoogleMaps\GoogleMapsService;
use KisCore\Infrastructure\Singleton;
use KisData\ConfigurationValues;
use KisData\StatusCode;
use KisData\ResponseModel;
use Tookan\Services\TookanAgentService;
use Tookan\Services\TookanTaskService;

class TaskService{

#Fields

    private $tookanTaskService;
    private $googleMapsService;
    private $tookanAgentService;

#


#Ctor

    public function __construct()
    {
        $this->tookanTaskService = Singleton::Create(TookanTaskService::class);
        $this->googleMapsService = Singleton::Create(GoogleMapsService::class);
        $this->tookanAgentService = Singleton::Create(TookanAgentService::class);
    }


#


# Method


    public function GetTaskByCustomerPhone($customerPhone){
        $tasks = $this->tookanTaskService->GetTasksNotCompletedByOrderId($customerPhone);
        if(Count($tasks) > 0){
            return new ResponseModel(StatusCode::Success , "You cannot create a new task because there is still a pending task", [
                "task_id" => $tasks[0]->job_id,
                "datetime" => $tasks[0]->job_pickup_datetime,
            ]);
        }

        return new ResponseModel(StatusCode::SuccessBut , "No previous request"); 
    }

    public function CreateTask($customerName, $customerPhone, $createdBy, $country, $address, $template = "", $customFields = []){

        $tasks = $this->tookanTaskService->GetTasksNotCompletedByOrderId($customerPhone);
        if(Count($tasks) > 0){
            return new ResponseModel(StatusCode::Pending , "You cannot create a new task because there is still a pending task", [
                "task_id" => $tasks[0]->job_id,
                "datetime" => $tasks[0]->job_pickup_datetime,
            ]);
        }

        $country = strtoupper($country);
        $location = $this->googleMapsService->GetLocationByAddress($address);
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
                return $this->AutoAsign($response->object()->data);
            }else{
                return new ResponseModel($response->object()->status, $response->object()->message);
            }

        }else{
            return new ResponseModel($response->status(), "There was an error creating the request");
        }
    }
    


#

#Utilities

    private function AutoAsign($job){

        if(!isset($job->geofence_details) || count($job->geofence_details) == 0){
            return new ResponseModel(StatusCode::SuccessBut, "The task has been created, but the automatic assignment was not completed because there is no geofence");
        }


        $agents = $this->tookanAgentService->GetAgentsbyJob($job);
        if(!isset($agents) || count($agents) == 0){
            return new ResponseModel(StatusCode::SuccessBut, "The task has been created, but there is no agent covering this area");
        }


        $startdate = $this->skipWeekend(strtotime(date("Y-m-d H:i:s")));
        $enddate = strtotime("+10 days", $startdate);

        while($startdate < $enddate){

            $agentIds = $this->tookanAgentService->GetAgentIdsByAgents($agents);

            $tasksAgentPerDay = $this->tookanTaskService->GetTasksByAgentIds($agentIds, $startdate);

            $bestAgentId = $this->tookanAgentService->GetAgentIdByMinNumberOfTasks($tasksAgentPerDay);

            if(count($tasksAgentPerDay[$bestAgentId]) < ConfigurationValues::NumberOfTasksPerDay){

                $res_edit_date_task = $this->tookanTaskService->UpdateTaskDate($job->job_id, $startdate);

                if($res_edit_date_task->ok()){
                    $this->tookanTaskService->AssignTaskToAgent($job->job_id, $bestAgentId);
                    return new ResponseModel(StatusCode::Success, "Successful", [
                        "task_id" => $job->job_id,
                        "datetime" => date("Y-m-d H:i:s", $startdate),
                    ]);
                }

            }else{
                $startdate = $this->skipWeekend(strtotime("+1 day", $startdate));
            }
        }

        return new ResponseModel(StatusCode::SuccessBut, "The task has been created, but the automatic assignment was not completed because of the orders pressure of the agents");

    }


    private function skipWeekend($date){
        $day = strtolower(date('l', $date));
        if($day == ConfigurationValues::Weekend){
            $date = strtotime("+1 day", $date);
        }

        return $date;
    }

#
}