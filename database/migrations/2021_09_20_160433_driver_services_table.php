<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DriverServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('driver_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->integer('fleet_id')->unsigned(); 
            $table->string('service_date');
            $table->string('service_mileage', 31);
            $table->string('service_comments');
            $table->string('next_service_mileage', 31);
            $table->tinyInteger('service_reminder')->default(0);
            $table->tinyInteger('tyres')->default(0);
            $table->tinyInteger('oil')->default(0);
            $table->tinyInteger('is_first');
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('driver_services');
    }
}
