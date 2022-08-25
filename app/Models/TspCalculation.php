<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TspCalculation extends Model
{
    protected $table = 'tsp_calculate';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id', 'year', 'g', 'f', 'c', 's', 'i'
    ];
    public $timestamps = false;
}
