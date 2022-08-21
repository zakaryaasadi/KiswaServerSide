<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewModel extends Model
{
    use HasFactory;

    protected $table = "review_models";
    protected $fillable = [
        'job_id', 
        'job_pickup_name', 
        'job_pickup_phone', 
        'job_pickup_address' ,
        'country', 
        'job_pickup_datetime', 
        'job_pickup_latitude', 
        'job_pickup_longitude',
        'fleet_id',
        'fleet_name',
        'creation_datetime',
        'acknowledged_datetime',
        'started_datetime',
        'arrived_datetime',
        'completed_datetime',
        'total_distance_travelled',

        'is_reply',
        'is_receipt',
        'price',
        'is_coupon',
        'service_rating',
        'fleet_rating',
    ];

}
