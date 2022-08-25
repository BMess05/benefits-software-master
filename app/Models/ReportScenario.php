<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportScenario extends Model
{
    protected $table = 'ReportScenario';
    protected $primaryKey = 'ReportScenarioId';
    protected $fillable = [
        'EmployeeId', 'ScenarioNo', 'RetirementDate', 'High3Average', 'AnnuityBeforeDeduction', 'SurvivorAnnuity', 'SurvivorAnnuityCost', 'PartTimeMultiplier', 'MRA10Multiplier', 'Annuity', 'IsSelected', 'CSRSServiceAtRetirement', 'FERSServiceAtRetirement'
    ];
    public $timestamps = false;
}
