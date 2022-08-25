<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppLookup extends Model
{
    protected $table = "AppLookup";
    protected $primaryKey = "AppLookupId";
    protected $fillable = [
        'AppLookupId', 'AppLookupTypeName', 'AppLookupName', 'AppLookupDescription', 'DisplayOrder', 'IsActive'
    ];
}
