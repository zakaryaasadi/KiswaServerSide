<?php

namespace Tookan\Services;

use KisCore\Infrastructure\Singleton;
use Tookan\Http\RegionApi;

class TookanAgentService{

#region Fields

    private $agentsList = [];
    private $regionApi;

#endregion


#region Ctor

public function __construct()
{
    $this->regionApi = Singleton::Create(RegionApi::class);
}

#endregion



#region Methods
    public function GetAgentsByJob($job){
        $geofenceDetails = json_decode($job->geofence_details);

        $this->agentsList = [];
        foreach($geofenceDetails as $i){
            $res = $this->regionApi->GetRegionById($i->region_id);
            
            if($res->ok()){
                $data = $res->object()->data;
                if(Count($data) > 0)
                    $this->AddAgentToList($data[0]->fleets);
            }
        }

        return $this->agentsList;
    }


    public function GetAgentIdsByAgents($agents){
        return collect($agents)
                ->map(function ($agent){
                    return $agent->fleet_id;
                });
    }


    public function GetAgentIdByMinNumberOfTasks($tasksWithAgents){

        reset($tasksWithAgents);


        $fleet_id = key($tasksWithAgents);
        $min =  PHP_INT_MAX;

        foreach($tasksWithAgents as $agentId => $tasks){
            $taskCount = count($tasks);
            if($taskCount < $min){
                $fleet_id = $agentId; 
                $min = $taskCount;
            }
        }

        return $fleet_id;
    }


#endregion



#region Utilities
    private function AddAgentToList($agents){
        foreach($agents as $i){
            $isExistsAgentsAtRegion = collect($this->agentsList)
                                        ->where('fleet_id', '=', $i->fleet_id)
                                        ->count() > 0;
            if(!$isExistsAgentsAtRegion){
                array_push($this->agentsList, $i);
            }
        }
    }

#endregion

}