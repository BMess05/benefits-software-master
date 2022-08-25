<?php

namespace App\Repositories;

use App\Models\Child;

class ChildRepository
{
    public function addNewChild($data = [])
    {
        if (!empty($data)) {
            $childObj = new Child();
            $childObj->EmployeeId = $data['empId'];
            $childObj->ChildName = $data['name'];
            $childObj->ChildAge = $data['age'];

            if ($childObj->save()) {
                return $childObj->ChildId;
            } else {
                return false;
            }
        }
    }

    public function getChildByParentId($id = null)
    {
        return Child::where('EmployeeId', $id)->get();
    }
}
