<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;
use KisCore\Infrastructure\Singleton;
use KisData\ResponseModel;
use KisServices\TaskService;

class TaskController extends Controller
{

#Fields
    private $taskService;
    private $rules;
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
    }

    public function GetAssignTaskByCustomerPhone($phone){
        $response = $this->taskService->GetAssignTaskByCustomerPhone($phone);
        return $response->toJson();
    }


    public function Create(Request $request){

           $validator = validator($request->all(), [
            'name' => 'required',
            'phone' => ['required', new PhoneNumber],
            'country' => 'required',
            'address' => 'required',
            'created_by' => 'required',
        ]);
           if($validator->fails()){
               $fatalData = new ResponseModel(400, "There are some errors in the request", $validator->errors());
               return $fatalData->toJson();
           }


            // $response = $this->taskService->CreateTask($request->name, $request->phone, $request->created_by, 
            //                 $request->country, $request->address, $request->template, $request->custom_fields);

                            
            // return $response->toJson();

    }

}
