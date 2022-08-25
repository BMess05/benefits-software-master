<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tsp extends Model
{
    protected $table = 'TSP';
    protected $primaryKey = 'EmployeeId';
    protected $fillable = [
        'EmployeeId', 'GFund', 'FFund', 'CFund', 'SFund', 'IFund', 'L2020', 'L2030', 'L2040', 'L2050', 'LIncome', 'GFundDist', 'FFundDist', 'CFundDist', 'SFundDist', 'IFundDist', 'L2020Dist', 'L2030Dist', 'L2040Dist', 'L2050Dist', 'LIncomeDist', 'ContributionRegular', 'ContributionCatchUp', 'EndingBalance', 'LoanRepayment', 'PayoffDate', 'StatementDate'
    ];
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
