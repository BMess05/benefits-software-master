<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NonDeductionService extends Model
{
    protected $table = 'NonDeductionService';
    protected $primaryKey = 'NonDeductionServiceId';
    protected $fillable = [
        'EmployeeId', 'FromDate', 'ToDate', 'DepositOwed', 'AmountOwed'
    ];
    public $timestamps = false;

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
