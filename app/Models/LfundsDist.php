<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LfundsDist extends Model
{
    protected $table = 'lfundDistribution';
    protected $fillable = ['date', 'lfund_type', 'gfund', 'ffund', 'cfund', 'sfund', 'ifund'];
}
