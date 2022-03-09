<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LoggerMiddleware;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => [LoggerMiddleware::class]], function () {


    Route::group(['prefix' => 'task'], function () {
        Route::post('/create', 'TaskController@Create');
        Route::Get('/get_assign_task/customer_phone/{phone}', 'TaskController@GetAssignTaskByCustomerPhone');
    });

    //Route::post('/task/create', 'TaskController@Create');
    // Route::get('/test', function(){
    //     return 'run';
    // });


});
