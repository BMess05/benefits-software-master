<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartTimeService extends Model
{
    protected $table = 'PartTimeService';
    protected $primaryKey = 'PartTimeServiceId';
    protected $fillable = [
        'EmployeeId', 'FromDate', 'ToDate', 'HoursWeek', 'percentage'
    ];
    public $timestamps = false;

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
