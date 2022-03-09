<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoAsignModel extends Model
{
    use HasFactory;

    protected $table = "auto_asign_queue";
    protected $fillable = ['job_id', 'job_pickup_name', 'job_pickup_address' , 'geofence_details'];

}
