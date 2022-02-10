<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fleet extends Model {
    protected $fillable = ['user_id', 'driver_id', 'van_number', 'insurance_company', 'insurance_expire_date', 'left_renewal', 'mot_expire_date', 'left_mot', 'comments'];
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}
