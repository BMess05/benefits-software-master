<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FEGLIDependent extends Model
{
    protected $table = "FEGLIDependent";
    protected $primaryKey = 'FEGLIDependentId';
    public $timestamps = false;
    protected $fillable = [
        'EmployeeId', 'DependentName', 'DateOfBirth', 'CoverAfter22'
    ];

    public function fegli() {
        return $this->belongsTo('App\Models\Fegli', 'EmployeeId');
    }
}
