<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DpdReport extends Model {
    protected $fillable = ['user_id', 'driver_id', 'dpdRoute', 'dpdDate', 'dpdStops', 'dpdPayType'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}
