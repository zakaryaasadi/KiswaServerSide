<?php

use App\Http\Controllers\EhsanController;
use App\Http\Controllers\GreenClosetLoginController;
use App\Http\Controllers\GreenClosetReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [GreenClosetLoginController::class, 'LoginIndex'])->name("login");

Route::post('/login', [GreenClosetLoginController::class, 'Login']);



Route::group(['middleware' => [AuthMiddleware::class]], function () {

    Route::get('/', function () {
        return view('welcome');
    });
    
    
    Route::get('/rating', [ReviewController::class, 'RatingView']);
    
    Route::get('/survey', [ReviewController::class, 'SurveyView']);
    
    Route::get('/reports', [GreenClosetReportController::class, 'Index'])->name("reports");


    Route::get("/ehsan-report", [EhsanController::class, "Index"])->name("ehsan");


});
