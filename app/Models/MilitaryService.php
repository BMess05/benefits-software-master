<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MilitaryService extends Model
{
    protected $table = 'MilitaryService';
    protected $primaryKey = 'MilitaryServiceId';
    public $timestamps = false;
    protected $fillable = [
        'EmployeeId', 'MilitaryServiceTypeId', 'FromDate', 'ToDate', 'IsRetired', 'DepositOwed', 'AmountOwed'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
