<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AppLookup;

class AppLookupController extends Controller
{
    public function addAppLookups()
    {
        $data['systemTypes'] = resolve('applookup')->getByTypeName('SystemType')->toArray();
        $data['retirementTypes'] = resolve('applookup')->getByTypeName('RetirementType')->toArray();
        $data['employeeTypes'] = resolve('applookup')->getByTypeName('EmployeeType')->toArray();
        $data['otherEmployeeTypes'] = resolve('applookup')->getByTypeName('OtherEmployeeType')->toArray();
        $data['marital_statuses'] = resolve('applookup')->getByTypeName('MaritalStatusType')->toArray();
        return view('admin.employee.addAppLookups', [
            'data' => $data,
            'active_tab' => '',
            'empId' => 1,
        ]);
    }
    public function saveAppLookups(Request $request)
    {
        $data = $request->all();
        if (!empty($data)) {
            unset($data['_token']);
            $validator = \Validator::make($request->all(), [
                'AppLookupTypeName' => 'required',
                'AppLookupName' => 'required',
                'AppLookupDescription' => 'required',
                'DisplayOrder' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
            }
            $obj = new AppLookup();
            $obj->AppLookupTypeName = $data['AppLookupTypeName'];
            $obj->AppLookupName = $data['AppLookupName'];
            $obj->AppLookupDescription = $data['AppLookupDescription'];
            $obj->DisplayOrder = $data['DisplayOrder'];
            if ($obj->save()) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Added!'
                ]);
            }
        }
    }
}
