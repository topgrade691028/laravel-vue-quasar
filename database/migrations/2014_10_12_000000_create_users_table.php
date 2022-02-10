<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->increments('id');
      $table->string('depot_location'); 
//      $table->string('avatar');
      $table->string('name');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('phone');
      $table->string('password');
//      $table->string('company');
      $table->string('full_name')->nullable();
      $table->string('country')->nullable();
      $table->string('address')->nullable();
      $table->string('zipcode')->nullable();
      $table->string('belongs')->nullable();
      $table->string('subcontractor', 10)->default('Regular');
      $table->tinyInteger('user_type')->default(0);
      $table->tinyInteger('is_active')->default(0);
      $table->integer('parent_id')->unsigned()->nullable();
      $table->string('user_roles', 50)->nullable();
      $table->rememberToken();
      $table->timestamps();
      // $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('users');
  }
}
