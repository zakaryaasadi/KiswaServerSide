<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LoggerMiddleware;
use App\Http\Middleware\ReviewLogger;

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


        Route::Get('/success', 'TaskController@Success');
        Route::post('/review', 'TaskController@Review');
    });

    //Route::post('/task/create', 'TaskController@Create');
    // Route::get('/test', function(){
    //     return 'run';
    // });


});


Route::group(['middleware' => [ReviewLogger::class]], function () {
    Route::post('/task/review', 'TaskController@Review');
});

Route::Get('/task/success', 'TaskController@Success');

Route::Get('/google/{latlng}', 'GoogleMapController@Get');
