<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $table = 'Deduction';
    protected $primaryKey = 'DeductionId';
    public $timestamps = false;
    protected $fillable = [
        'EmployeeId', 'DeductionName', 'DeductionAmount', 'IsOther'
    ];

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
