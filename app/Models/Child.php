<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $table = "Child";
    protected $primaryKey = "ChildId";
    public $timestamps = false;
    protected $fillable = [
        'EmployeeId', 'ChildName', 'ChildAge'
    ];
}
