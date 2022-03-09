<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoAsignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_asign_queue', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->string('job_pickup_name')->default('');
            $table->string('job_pickup_address')->default('');
            $table->string('geofence_details')->default('');
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
        Schema::dropIfExists('auto_asign_queue');
    }
}
