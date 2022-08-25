<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryColumn extends Model
{
    protected $table = "history_columns";
    protected $fillable = [
        'column_name', 'table_name', 'tab_name'
    ];

    protected $primaryKey = "id";

    public function historyChanges()
    {
        return $this->hasMany('App\Models\HistoryChange', 'column_id');
    }
}
