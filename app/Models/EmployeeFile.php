<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeFile extends Model
{
    protected $table = 'EmployeeFile';
    protected $primaryKey = 'EmployeeFileId';
    protected $fillable = [
        'EmployeeId', 'StoredFileName', 'OrigFileName', 'ContentType', 'FileSize', 'IsActive'
    ];

    public $timestamps = false;

    public function employee() {
        return $this->belongsTo('App\Models\Employee', 'EmployeeId');
    }
}
