<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryChange extends Model
{
    protected $table = "history_changes";
    protected $fillable = [
        'emp_id', 'column_id', 'row_id	', 'old_val', 'new_val', 'updated_by'
    ];
    protected $primaryKey = 'id';
    public function historyColumn()
    {
        return $this->belongsTo('App\Models\HistoryColumn', 'column_id');
    }

    public function updated_by_user()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }
}
