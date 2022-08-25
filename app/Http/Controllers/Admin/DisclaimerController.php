<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DisclaimerRequest;

class DisclaimerController extends Controller
{
	public function add_disclaimer()
	{
		return view('admin.disclaimer.add_disclaimer');
	}

	public function save_disclaimer(DisclaimerRequest $request)
	{
		$data = $request->all();
		unset($data['_token']);
		$result = resolve('disclaimers')->addNewDisclaimers($data);
		if ($result) {
			return redirect('dashboard/disclaimers')->with(['status' => 'success', 'message' => 'Disclaimer added successfully']);
		} else {
			return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
		}
	}

	public function edit_disclaimer($did = null)
	{
		$did = base64_decode($did);
		$disclaimer = resolve('disclaimers')->getDisclaimerById($did);
		return view('admin.disclaimer.edit_disclaimer', [
			'disclaimer' => $disclaimer
		]);
	}

	public function update($empid = null, DisclaimerRequest $request) {
		$data = $request->all();
		$empid = base64_decode($empid);
        unset($data['_token']);
        $result = resolve('disclaimers')->updateDisclaimer($empid, $data);
        if($result) {
            return redirect('dashboard/disclaimers')->with(['status' => 'success', 'message' => 'Disclaimers updated successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

	public function delete_disclaimer($id = null) {
		$disclaimer = resolve('disclaimers')->getDisclaimerById($id);
		if(!$disclaimer) {
			return redirect('dashboard/disclaimers')->with(['status' => 'danger', 'message' => 'Invalid Input']);
		}
		$result = resolve('disclaimers')->deleteDisclaimer($id);
        if($result) {
            return redirect('dashboard/disclaimers')->with(['status' => 'success', 'message' => 'Disclaimers deleted successfully']);
        }   else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
	}
}
