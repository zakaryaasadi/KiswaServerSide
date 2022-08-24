<?php

namespace App\Http\Controllers;

use App\Models\ReviewModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use KisData\ResponseModel;
use KisData\StatusCode;

class ReviewController extends Controller
{


    private $review_rules = [
        'job_id' => 'required',
    ];
    
    public function RatingView(){
        $fleetsRating = DB::table('review_models')
                    ->select(DB::raw('fleet_id, fleet_name, country, avg(fleet_rating) as fleet_rating'))
                    ->where('fleet_rating', '<>', 0)
                    ->groupBy('fleet_id', 'fleet_name', 'country')
                    ->get();

        $countries = DB::table('review_models')
                    ->select(DB::raw('DISTINCT country as country_name'))
                    ->get();

        $countriesRating = DB::table('review_models')
                    ->select(DB::raw('country, avg(service_rating) as service_rating'))
                    ->where('service_rating', '<>', 0)
                    ->groupBy('country')
                    ->get();


        return view('rating', [
            "fleetsRating"  => $fleetsRating,
            "countries" => $countries,
            "countriesRating" => $countriesRating,
        ]);

    }

    public function SurveyView(){
        $countries = DB::table('review_models')
                    ->select(DB::raw('DISTINCT country as country_name'))
                    ->get();

        

        return view('survey', [
                        "countries" => $countries,
                    ]);
    }


    public function SurveyTable(Request $request){

        $reviews = DB::table('review_models');

        $search = $request->search['value'];
        if($search != null || $search != ""){
            $reviews = $reviews->where(function ($query) use ($search) {
                $query->where('job_id', '=', $search)
                      ->orWhere('job_pickup_name', '=', $search)
                      ->orWhere('job_pickup_phone', '=', $search)
                      ->orWhere('fleet_name', '=', $search)
                      ->orWhere('completed_datetime', '=', $search);
            });

        }

        $searchCountry = $request->columns[4]['search']['value'];
        if($searchCountry != null || $searchCountry != ""){
            $reviews = $reviews->where('country', '=', $searchCountry);
        }

        $recordsFiltered = $reviews->count();

        $reviews = $reviews->skip($request->start)
                    ->take($request->length)
                    ->orderByDesc('completed_datetime');
        
        $data = $reviews->get();
        foreach($data as $item){
            $item->location = "https://maps.google.com/?q=" . $item->job_pickup_latitude ."," . $item->job_pickup_longitude;
            $item->is_receipt = $item->is_reply == 0 ? 2 : $item->is_receipt;
            $item->is_coupon = $item->is_reply == 0 ? 2 : $item->is_coupon;
            $item->acknowledged_datetime = date("Y-m-d h:i:s a", strtotime($item->acknowledged_datetime));
            $item->started_datetime = date("Y-m-d h:i:s a", strtotime($item->started_datetime));
            $item->arrived_datetime = date("Y-m-d h:i:s a", strtotime($item->arrived_datetime));
            $item->completed_datetime = date("Y-m-d h:i:s a", strtotime($item->completed_datetime));
        }

        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => ReviewModel::count(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ]);

    }




    public function ReviewEdit(Request $request){
        $validator = validator($request->all(), $this->review_rules);
           if($validator->fails()){
               $fatalData = new ResponseModel(StatusCode::Failed, "There are some errors in the request", $validator->errors());
               return $fatalData->toJson();
           }


           $review = ReviewModel::where('job_id', $request->job_id)->first();
           if($review == null){
                $fatalData = new ResponseModel(StatusCode::SuccessBut, "There is no record!");
                return $fatalData->toJson();
           }

           foreach($request->all() as $key => $value){
               $review->$key = $value;
           }

           $review->save();


           $response = new ResponseModel(StatusCode::Success, "Success");
           return $response->toJson();

    }
}
