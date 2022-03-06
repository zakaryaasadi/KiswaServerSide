<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use KisCore\Infrastructure\Singleton;
use KisData\ResponseModel;
use KisServices\TaskService;

class TaskController extends Controller
{

#Fields
    private $taskService;
#
    public function __construct()
    {
        $this->taskService = Singleton::Create(TaskService::class);   
    }


    public function Create(Request $request){

            $validator = Validator::make($request->all(), $this->rules);
            if($validator->failed()){
                $fataData = new ResponseModel(400, "There are some errors in the request", $validator->errors());
                return $fataData->toJson();
            }

            $response = $this->taskService->CreateTask($request->input('name'), $request->input('phone'), $request->input('created_by'), 
                            $request->input('country'), $request->input('address'), $request->input('template'), $request->input('custom_fields'));

                            
            return $response->toJson();
    }



#Properties
    public $rules = [
        'name' => 'require',
        'phone' => 'require|phone_number',
        'country' => 'require',
        'address' => 'require',
        'created_by' => 'require',
    ];
#
}
