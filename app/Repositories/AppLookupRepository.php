<?php

namespace App\Repositories;

use App\Models\AppLookup;

class AppLookupRepository
{
    public function getById($id = null)
    {
        return AppLookup::where('AppLookupId', $id)->first();
    }

    public function getByTypeName($typeName = null)
    {
        return AppLookup::where('AppLookupTypeName', $typeName)->orderBy('DisplayOrder', 'ASC')->get();
    }

    public function getValueByAppLookupName($name = null)
    {
        $value = AppLookup::select('AppLookupDescription')->where('AppLookupName', $name)->first();
        if ($value) {
            return $value->AppLookupDescription;
        }
        return 0;
    }
}
