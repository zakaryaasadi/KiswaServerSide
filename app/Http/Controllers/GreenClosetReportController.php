<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use KisCore\Infrastructure\Singleton;
use Tookan\DefaultValues\TookanCountries;
use Tookan\DefaultValues\TookanTaskStatus;
use Tookan\Services\TookanTaskService;

class GreenClosetReportController extends Controller
{

    #Fields
        private $tookanTaskService;
    #

    public function __construct()
    {
        $this->tookanTaskService = Singleton::Create(TookanTaskService::class);

    }

    public function Index(){
        return view('green_closet_report');
    }


    // params: start_date - end_date - job_id - order_id

    public function GetTotalReport(Request $request){ 
        $start_date = date("Y-m-d", strtotime($request->start_date));
        $end_date = date("Y-m-d", strtotime($request->end_date));

        $total_page_count = -1;
        $page = 1;
        $data = [
            "donate_weights" => 0,
            "sell_weights" => 0,
            "number_of_donates" => 0,
            "number_of_sells" => 0,
        ];


        $body = [
            "job_status" => [TookanTaskStatus::Successful],
            "start_date" => $start_date,
            "end_date" => $end_date,
            "is_pagination" => 1,
            "team_id" => TookanCountries::$Values["GREEN_CLOSET:KSA"]["TEAM_ID"],
            "custom_fields" => 1,
        ];

        if($request->job_id != null || $request->job_id != ""){
            $body["job_id"] = [$request->job_id];
        }

        if($request->order_id != null || $request->order_id != ""){
            $body["order_id"] = [$request->order_id];
        }

        do{
            
            $body["requested_page"] = $page;
            $response = $this->tookanTaskService->GetTasks($body);
            if($response->ok() && $response->object()->status == 200){

                $total_page_count = $response->object()->total_page_count;

                $orders = $response->object()->data;

                foreach($orders as $order){
                    $weight = 0;
                    $is_sell = false;

                    foreach($order->fields->custom_field as $field){

                        if($field->label == "weight"){
                            $weight = $field->data;
                        }
        
                        if($field->label == "order_type"){
                            $is_sell = strtoupper($field->data) == "SELL";
                        }

                    }

                    if($is_sell){
                        $data["number_of_sells"]++;
                        $data["sell_weights"] += $weight;

                    }else{
                        $data["number_of_donates"]++;
                        $data["donate_weights"] += $weight;
                    }
                }
            }


        }while($page++ < $total_page_count);

        return json_encode($data);
    }



    public function ReportTable(Request $request){

        $start_date = date("Y-m-d", strtotime($request->start_date));
        $end_date = date("Y-m-d", strtotime($request->end_date));
        $page = $request->start / 100 + 1;
        

        $body = [
            "job_status" => [TookanTaskStatus::Successful],
            "start_date" => $start_date,
            "end_date" => $end_date,
            "is_pagination" => 1,
            "requested_page" => $page,
            "team_id" => TookanCountries::$Values["GREEN_CLOSET:KSA"]["TEAM_ID"],
            "custom_fields" => 1,
        ];
        

        // $search = $request->search['value'];
        // if($search != null || $search != ""){
            
        // }


        // $job_id = $request->columns[4]['search']['value'];
        // if($job_id != null || $job_id != ""){
        //     $body["job_id"] = [$job_id];
        // }



        $response = $this->tookanTaskService->GetTasks($body);
        if($response->ok() && $response->object()->status == 200 ){
            $data = $this->processData($response->object()->data);
            $dataFiltered = $data;

            $order_type = $request->columns[6]['search']['value'];
            if($order_type != null || $order_type != ""){
                $dataFiltered = $this->filterData($data, $order_type);
            }

            return json_encode([
                "draw" => $request->draw,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($dataFiltered),
                "data" => $dataFiltered,
            ]);
        }
        
       

        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
        ]);

    }


    private function processData($data){
        foreach($data as $item){
            $item->location = "https://maps.google.com/?q=" . $item->job_pickup_latitude ."," . $item->job_pickup_longitude;

            foreach($item->fields->custom_field as $field){
                if($field->label == "weight"){
                    $item->weight = $field->data;
                }

                if($field->label == "order_type"){
                    $item->order_type = $field->data;
                }

                if($field->label == "charity"){
                    $item->charity = $field->data;
                }
            }
        }

        return $data;
    }


    private function filterData($data, $order_type){
        $result = [];

        foreach($data as $item){
            if(strtoupper( $item->order_type ) == strtoupper($order_type)){
                array_push($result, $item);
            }
        }

        return $result;
    }

    
}
