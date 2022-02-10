<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model {
    protected $fillable = ['user_id', 'driver_id', 'card_no', 'company'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}
