<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disclaimer extends Model
{
    protected $table = "Disclaimer";
    protected $primaryKey = "DisclaimerId";
    public $timestamps = false;
    protected $fillable = [
        'DisclaimerName', 'DisclaimerText', 'IsDefault', 'IsActive'
    ];

    public function advisor() {
        return $this->hasMany('App\Models\Advisor', 'DefaultDisclaimerId');
    }
}
