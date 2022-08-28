<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('review_models', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->string('job_pickup_name')->default('');
            $table->string('job_pickup_phone')->default('');
            $table->string('job_pickup_address')->default('');
            $table->string('job_pickup_datetime')->default('');
            $table->string('country')->default('');
            $table->float('job_pickup_latitude', 10, 7)->default(0);
            $table->float('job_pickup_longitude', 10, 7)->default(0);
            $table->integer('fleet_id');
            $table->string('fleet_name')->default('');
            $table->string('creation_datetime')->default('');
            $table->string('acknowledged_datetime')->default('');
            $table->string('started_datetime')->default('');
            $table->string('arrived_datetime')->default('');
            $table->string('completed_datetime')->default('');
            $table->integer('total_distance_travelled')->nullable()->default(0);
            
            $table->integer('is_reply')->default(-1);
            $table->integer('is_receipt')->default(-1);
            $table->string('price')->default('-');
            $table->integer('is_coupon')->default(-1);
            $table->integer('service_rating')->default(0);
            $table->integer('fleet_rating')->default(0);

            
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_models');
    }
}
