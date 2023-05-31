<?php

namespace Tookan\Http;

use Tookan\DefaultValues\TookanJobType;

class TaskApi{


    public function GetTasks($body){
        $body['job_type'] = [TookanJobType::PickUp, TookanJobType::Delivery, TookanJobType::Appointment, TookanJobType::FOS];
        
        if(!isset($body['start_date'])){
            $body["start_date"] = date("Y-m-d", strtotime('-4 months'));
        }

        if(!isset($body["end_date"])){
            $body['end_date'] = date("Y-m-d", strtotime('+1 months'));
        }

        return TookanApi::Callback("get_all_tasks", $body);
    }


    public function CreateTask($params){
        $defaultValues = [
            "has_pickup" => "1",
            "has_delivery" => "0",
            "layout_type" =>"0",
            "tracking_link" => 1,
            "geofence" => 1,
            "pickup_custom_field_template" => "customFields",
        ];

        $body = array_merge($params, $defaultValues);
        return TookanApi::Callback("create_task", $body);
    }


    public function AssignTaskToAgent($jobId, $fleetId, $teamId){
        $body = [
            "job_id" => $jobId,
            "fleet_id" => $fleetId,
            "team_id" => $teamId,
        ];
        return TookanApi::Callback('assign_task', $body);
    }


    public function UpdateTaskDate($jobId, $date){
        $d = date("Y-m-d H:i:s", $date);
        $body = [
            "job_ids" => [$jobId],
            "layout_type" => 0,
            "start_time" => $d,
            "end_time" => $d
            ];

        return TookanApi::Callback('change_job_date', $body);
    }
}