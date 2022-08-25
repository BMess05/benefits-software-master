<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisor extends Model
{
    protected $table = 'Advisor';

    protected $primaryKey = 'AdvisorId';

    protected $fillable = [
        'AdvisorId', 'AdvisorName', 'AdvisorAddress', 'PhoneNumber', 'FaxNumber', 'DefaultDisclaimerId', 'IsActive', 'SuppressConfidential'
    ];

    public function employee() {
        return $this->hasMany(Employee::class, 'AdvisorId');
    }

    public function disclaimer() {
        return $this->belongsTo('App\Models\Disclaimer', 'DefaultDisclaimerId');
    }
}
