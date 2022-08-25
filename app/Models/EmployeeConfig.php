<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeConfig extends Model
{
    protected $table = 'EmployeeConfig';
    protected $primaryKey = 'EmployeeConfigId';
    protected $fillable = [
        'EmployeeId', 'ConfigType', 'ConfigValue'
    ];

    public $timestamps = false;

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
