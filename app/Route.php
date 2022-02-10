<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Report;

class Route extends Model {
    protected $fillable = ['route_number'];
    
    public function reports()
    {
        return $this->hasMany('App\Report');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
