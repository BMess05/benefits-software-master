<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fegli extends Model
{
    protected $table = "FEGLI";
    protected $primaryKey = "EmployeeId";
    protected $fillable = [
        'EmployeeId', 'BasicInc', 'BasicAmount', 'OptionAInc', 'OptionAAmount', 'OptionBInc', 'OptionBMultiplier', 'OptionBAmount', 'OptionCInc', 'OptionCMultiplier', 'OptionCAmount', 'TotalAmount', 'SalaryForFEGLI', 'DoesNotMeetFiveYear', 'basicReductionAfterRetirement', 'optionAReductionAfterRetirement', 'optionBReductionAfterRetirement', 'optionCReductionAfterRetirement'
    ];
    public $timestamps = false;
    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
    public function fegliDependent() {
        return $this->hasMany('App\Models\FEGLIDependent', 'EmployeeId');
    }
}
