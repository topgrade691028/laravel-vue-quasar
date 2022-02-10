<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {
    protected $fillable = ['report_title, report_date'];
    public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
    public function route()
    {
        return $this->belongsTo('App\Route');
    }
}
