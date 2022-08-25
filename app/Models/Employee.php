<?php

namespace App\Models;

use App\Models\AppLookup;
use App\Models\EmployeeConfig;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Employee extends Model
{
    protected $table = 'Employee';

    protected $primaryKey = 'EmployeeId';

    protected $fillable = [
        'EmployeeId', 'EmployeeName', 'AdvisorId', 'DueDate', 'ReportDate', 'EmployeeAddress', 'SystemTypeId', 'RetirementTypeId', 'EmployeeTypeId', 'OtherEmployeeTypeId', 'PostalEmployee', 'MaritalStatusTypeId', 'SpouseName', 'CurrentSalary', 'High3Average', 'UnusedSickLeave', 'UnusedAnnualLeave', 'HealthPremium', 'DentalPremium', 'VisionPremium', 'SSMonthlyAt62', 'SSStartAge', 'SSMonthlyAtStartAge', 'SSAtAgeOfRetirement', 'SSYearsEarning', 'SSSpousalBenefit', 'DisclaimerId', 'MaxReportingAge', 'IsActive', 'FERSTransferDate', 'CSRSOffsetDate', 'SSEligible', 'FLTCIPPremium', 'DateReceived', 'DateCompleted', 'SpecialProvisionsDate', 'DoesNotMeetFiveYear', 'created_by', 'updated_by', 'notes'
    ];

    protected $appends = ['EmployeeType', 'RetirementType', 'SystemType', 'MaritalStatusType', 'AnnuityCola', 'CreatedByName', 'UpdatedByName'];

    public function getAnnuityColaAttribute()
    {
        if ($this->SystemType == "FERS") {
            $cola = EmployeeConfig::select('ConfigValue as system_cola')->where(['EmployeeId' => $this->EmployeeId, 'ConfigType' => 'FERSCola'])->first();
            if (!$cola) {
                $cola = AppLookup::select('AppLookupDescription as system_cola')->where('AppLookupName', 'FERSCola')->first();
            }
        } else {
            $cola = EmployeeConfig::select('ConfigValue as system_cola')->where(['EmployeeId' => $this->EmployeeId, 'ConfigType' => 'CSRSCola'])->first();
            if (!$cola) {
                $cola = AppLookup::select('AppLookupDescription as system_cola')->where('AppLookupName', 'CSRSCola')->first();
            }
        }
        if ($cola) {
            $system_cola = $cola->system_cola ?? '';
        } else {
            $system_cola = '';
        }
        return $system_cola;
    }
    // Isn't it lovely, Al alone... Tear me to pieces.. skin and bone... Hello, Welcome home

    public function getEmployeeTypeAttribute()
    {
        $employeeType = AppLookup::select('AppLookupName as employeeType')->where('AppLookupId', $this->EmployeeTypeId)->first();
        if ($employeeType) {
            $employeeType = $employeeType->employeeType ?? '';
        } else {
            $employeeType = '';
        }
        return $employeeType;
    }

    public function getRetirementTypeAttribute()
    {
        $retirementType = AppLookup::select('AppLookupName as retirementType')->where('AppLookupId', $this->RetirementTypeId)->first();
        if ($retirementType) {
            $retirementType = $retirementType->retirementType ?? '';
        } else {
            $retirementType = '';
        }
        return $retirementType;
    }

    public function getSystemTypeAttribute()
    {
        $systemType = AppLookup::select('AppLookupName as systemType')->where('AppLookupId', $this->SystemTypeId)->first();
        if ($systemType) {
            $systemType = $systemType->systemType ?? '';
        } else {
            $systemType = '';
        }
        return $systemType;
    }

    public function getMaritalStatusTypeAttribute()
    {
        $maritalStatusTypeType = AppLookup::select('AppLookupName as maritalStatusType')->where('AppLookupId', $this->MaritalStatusTypeId)->first();
        if ($maritalStatusTypeType) {
            $maritalStatusTypeType = $maritalStatusTypeType->maritalStatusType ?? '';
        } else {
            $maritalStatusTypeType = '';
        }
        return $maritalStatusTypeType;
    }

    public function getCreatedByNameAttribute()
    {
        $user = User::find($this->created_by);
        if ($user) {
            return $user->name;
        } else {
            return "";
        }
    }

    public function getUpdatedByNameAttribute()
    {
        $user = User::find($this->updated_by);
        if ($user) {
            return $user->name;
        } else {
            return "";
        }
    }

    public function advisor()
    {
        return $this->belongsTo(Advisor::class, 'AdvisorId');
    }
    public function eligibility()
    {
        return $this->hasOne('App\Models\Eligibility', 'EmployeeId');
    }
    public function fegli()
    {
        return $this->hasOne('App\Models\Fegli', 'EmployeeId');
    }
    public function militaryService()
    {
        return $this->hasMany('App\Models\MilitaryService', 'EmployeeId');
    }
    public function nonDeductionService()
    {
        return $this->hasMany('App\Models\NonDeductionService', 'EmployeeId');
    }

    public function refundedService()
    {
        return $this->hasMany('App\Models\RefundedService', 'EmployeeId');
    }

    public function tsp()
    {
        return $this->hasOne('App\Models\Tsp', 'EmployeeId');
    }
    public function deduction()
    {
        return $this->hasMany('App\Models\Deduction', 'EmployeeId');
    }
    public function employeeConfig()
    {
        return $this->hasMany('App\Models\EmployeeConfig', 'EmployeeId');
    }
    public function employeeFiles()
    {
        return $this->hasMany('App\Models\EmployeeFile', 'EmployeeId');
    }
    public function lookup()
    {
        return $this->belongsTo('App\Models\AppLookup', 'SystemTypeId');
    }

    public function getAdvisorsOnSearch($name)
    {
        return $this->belongsTo(Advisor::class, 'AdvisorId')->where('AdvisorName', 'LIKE', "%$name%");
    }
    public function partTimeServices()
    {
        return $this->hasMany('App\Models\PartTimeService', 'EmployeeId');
    }
}
