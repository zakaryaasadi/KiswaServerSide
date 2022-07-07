<?php

namespace KisServices;

use App\Models\AutoAsignModel;
use Illuminate\Support\Facades\Log;
use KisCore\Infrastructure\Singleton;
use Tookan\DefaultValues\TookanCountries;
use Tookan\Services\TookanAgentService;
use Tookan\Services\TookanTaskService;

class AutoAsignQueueService{

#Fields
    private $isProcessing;
    private $tookanTaskService;
    private $tookanAgentService;
#


#Ctor
    public function __construct()
    {
        $this->isProcessing = false;
        $this->tookanTaskService = Singleton::Create(TookanTaskService::class);
        $this->tookanAgentService = Singleton::Create(TookanAgentService::class);
    }
#

    public function IsProcessing(){
        return $this->isProcessing;
    }

    public function AddToQueue($job, $country){
        if(!isset($job->geofence_details) || count($job->geofence_details) == 0){
            return;
        }

        $job->geofence_details = json_encode($job->geofence_details);
        $job->country = $country;

        AutoAsignModel::Create((array)$job);
    }

    public function Run(){
        Log::channel('auto_asign')->info('Run auto asign queue');

        $this->isProcessing = true;
        $jobs = AutoAsignModel::get();
        Log::channel('auto_asign')->info('Number of jobs is ' . count($jobs));
        foreach($jobs as $job){
            $this->processing($job);
            $job->delete();
        }
        $this->isProcessing = false;
        Log::channel('auto_asign')->info('Finish auto asign queue');
    }


 #Utilities

    private function Processing($job){

        //$job = json_decode(json_encode($jobAsArray), false);

        $agents = $this->tookanAgentService->GetAgentsbyJob($job);
        if(!isset($agents) || count($agents) == 0){
            Log::channel('auto_asign')->info("The task has been created, but there is no agent covering this area");
        }

        $initDate = $this->createStartDateTask($job->country);
        $startdate = $this->skipWeekend($initDate, $job->country);
        $enddate = strtotime("+10 days", $startdate);

        while($startdate < $enddate){

            $agentIds = $this->tookanAgentService->GetAgentIdsByAgents($agents);

            $tasksAgentPerDay = $this->tookanTaskService->GetTasksByAgentIds($agentIds, $startdate);

            $bestAgentId = $this->tookanAgentService->GetAgentIdByMinNumberOfTasks($tasksAgentPerDay);

            if(count($tasksAgentPerDay[$bestAgentId]) < TookanCountries::$Values[$job->country]["NUMBER_OF_TASKS"]){

                $res_edit_date_task = $this->tookanTaskService->UpdateTaskDate($job->job_id, $startdate);

                if($res_edit_date_task->ok()){
                    $this->tookanTaskService->AssignTaskToAgent($job, $bestAgentId);

                    Log::channel('auto_asign')->info("Successful " . json_encode([
                        "task_id" => $job->job_id,
                        "datetime" => date("Y-m-d H:i:s", $startdate),
                    ]));

                    return;
                }

            }else{
                $startdate = $this->skipWeekend(strtotime("+1 day", $startdate), $job->country);
            }
        }

        Log::channel('auto_asign')->info("The task has been created, but the automatic assignment was not completed because of the orders pressure of the agents");

    }



    private function createStartDateTask($country){
        date_default_timezone_set(TookanCountries::$Values[$country]["TIMEZONE_CODE"]);

        $startHour = TookanCountries::$Values[$country]["START_HOUR_WORK"];
        $endHour = TookanCountries::$Values[$country]["END_HOUR_WORK"];

        $afterOneHour = date("H") + 1;
    
        // just in eid
        $initDate = strtotime("07/12/2022 {$afterOneHour}:00");
        if($afterOneHour > $endHour){
            return strtotime("+1 day {$startHour}:00", $initDate);
        }elseif($afterOneHour < $startHour){
            return strtotime("{$startHour}:00", $initDate);
        }
    
        return strtotime('+1 hour', $initDate);
    }


    private function skipWeekend($date, $country){
        $weekend = TookanCountries::$Values[$country]["WEEKEND"];
        
        $weekend = strtolower($weekend);
        $day = strtolower(date('l', $date));

        if($day == $weekend){
            $date = strtotime("+1 day", $date);
        }

        return $date;
    }

#

}