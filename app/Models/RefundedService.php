<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundedService extends Model
{
    protected $table = 'RefundedService';
    protected $primaryKey = 'RefundedServiceId';
    protected $fillable = [
        'EmployeeId', 'FromDate', 'ToDate', 'Withdrawal', 'Redeposit', 'AmountOwed'
    ];
    public $timestamps = false;
    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
