<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail{
    use HasApiTokens, Notifiable;

//    protected $fillable = ['name', 'avatar', 'email', 'phone', 'password', 'company', 'full_name', 'zipcode', 'user_type', 'is_active'];
    protected $fillable = ['name', 'email', 'password', 'phone', 'full_name', 'depot_location', 'belongs', 'parent_id', 'user_roles', 'country', 'address', 'zipcode', 'subcontractor', 'user_type', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    public function reports() {
        return $this->hasMany('App\Report');
    }
    public function routes() {
        return $this->hasMany('App\Route');
    }
    public function drivers() {
        return $this->hasMany('App\Driver');
    }
}
