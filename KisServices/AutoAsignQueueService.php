<?php

namespace KisServices;

use App\Models\AutoAsignModel;
use Illuminate\Support\Facades\Log;
use KisCore\Infrastructure\Singleton;
use KisData\ConfigurationValues;
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

    public function AddToQueue($job){
        if(!isset($job->geofence_details) || count($job->geofence_details) == 0){
            return;
        }

        $job->geofence_details = json_encode($job->geofence_details);
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
                    Log::channel('auto_asign')->info("Successful " . json_encode([
                        "task_id" => $job->job_id,
                        "datetime" => date("Y-m-d H:i:s", $startdate),
                    ]));
                    return;
                }

            }else{
                $startdate = $this->skipWeekend(strtotime("+1 day", $startdate));
            }
        }

        Log::channel('auto_asign')->info("The task has been created, but the automatic assignment was not completed because of the orders pressure of the agents");

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