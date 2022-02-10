<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->integer('driver_id')->unsigned(); 
            $table->string('van_number');
            $table->string('insurance_company', 31);
            $table->string('insurance_expire_date', 31);
            $table->integer('left_renewal');
            $table->string('mot_expire_date', 31);
            $table->integer('left_mot');
            // $table->tinyInteger('mot_reminder');
            // $table->string('insurance_reminder');
            $table->string('comments', 512);
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('driver_id')
                ->references('id')->on('drivers')
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
        Schema::dropIfExists('fleets');
    }
}
