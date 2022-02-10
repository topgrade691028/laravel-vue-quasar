<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model {
    protected $fillable = ['billingYear', 'firstTransactionDate', 'lastTransactionDate', 'previoudsCutOffDate', 'cutOffDate', 'duration'];
}
