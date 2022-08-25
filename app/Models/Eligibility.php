<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eligibility extends Model
{
    protected $table = "Eligibility";
    protected $primaryKey = "EmployeeId";
    public $timestamps = false;
    protected $fillable = [
        'EmployeeId', 'DateOfBirth', 'LeaveSCD', 'EligibilitySCD', 'AnnuitySCD', 'MinServiceYear', 'MinServiceMonth', 'MinRetirementYear', 'MinRetirementMonth', 'MinRetirementDate', 'RetirementDate', 'DepositPenalty', 'RefundPenalty', 'PartTimeMultiplier', 'Catch62'
    ];
    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
