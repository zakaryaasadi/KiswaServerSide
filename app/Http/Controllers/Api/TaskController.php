<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;
use KisCore\Infrastructure\Singleton;
use KisData\ResponseModel;
use KisData\StatusCode;
use KisServices\TaskService;

class TaskController extends Controller
{

#Fields
    private $taskService;
    private $rules;
    private $review_rules;
#
    public function __construct()
    {
        $this->taskService = Singleton::Create(TaskService::class);
        $this->rules = [
            'name' => 'required',
            'phone' => ['required', new PhoneNumber],
            'country' => 'required',
            'address' => 'required',
            'created_by' => 'required',
        ];

        $this->review_rules = [
            'job_id' => 'required',
        ];
    }

    public function GetAssignTaskByCustomerPhone($phone){
        $response = $this->taskService->GetAssignTaskByCustomerPhone($phone);
        return $response->toJson();
    }


    public function Create(Request $request){

           $validator = validator($request->all(), $this->rules);
           if($validator->fails()){
               $fatalData = new ResponseModel(StatusCode::Failed, "There are some errors in the request", $validator->errors());
               return $fatalData->toJson();
           }


            $response = $this->taskService->CreateTask($request->name, $request->phone, $request->created_by, 
                            $request->country, $request->address, $request->template, $request->custom_fields);

                            
            return $response->toJson();

    }

}
