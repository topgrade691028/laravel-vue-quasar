<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('full_name')->nullable();
            $table->string('phone');
            $table->string('driver_name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('pay_type', 15)->default('fixed');
            $table->string('pay_amount', 10);
            $table->tinyInteger('has_access')->default(1);
            $table->string('deleted_date')->nullable();
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
        Schema::dropIfExists('drivers');
    }
}
