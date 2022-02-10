<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverService extends Model {
    protected $fillable = ['user_id', 'fleet_id', 'service_date', 'service_mileage', 'service_comments', 'next_service_mileage', 'service_reminder', 'tyres', 'oil', 'is_first'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function fleet()
    {
        return $this->belongsTo('App\Fleet');
    }
}
