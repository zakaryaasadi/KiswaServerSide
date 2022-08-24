<?php

use App\Http\Controllers\ReviewController;
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
    });

});


Route::group(['middleware' => [ReviewLogger::class]], function () {

    Route::post('/task/review', [ReviewController::class, 'ReviewEdit']);

});


Route::post('/survey-table', [ReviewController::class, 'SurveyTable']);

Route::Get('/google/{latlng}', 'GoogleMapController@Get');
