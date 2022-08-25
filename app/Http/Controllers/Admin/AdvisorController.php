<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdvisorRequest;

class AdvisorController extends Controller
{
    public function add_advisor()
    {
        $disclaimers = resolve('advisor')->getAllDisclaimers();
        return view('admin.advisor.add_advisor', [
            'disclaimers' => $disclaimers
        ]);
    }
    public function save(AdvisorRequest $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $result = resolve('advisor')->addNewAdvisor($data);
        if ($result) {
            return redirect('/advisor/details/' . $result)->with(['status' => 'success', 'message' => 'Advisor added successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }
    public function getDetails($adId = null)
    {
        $advisor = resolve('advisor')->getById($adId);
        // echo "<pre>"; print_r($advisor); exit;
        return view('admin.advisor.advisorDetails', [
            'advisor' => $advisor
        ]);
    }
    public function edit($adId = null)
    {
        $advisor = resolve('advisor')->getById($adId);
        $disclaimers = resolve('advisor')->getAllDisclaimers();
        return view('admin.advisor.edit', [
            'advisor' => $advisor,
            'disclaimers' => $disclaimers
        ]);
    }
    public function update($adId = null, AdvisorRequest $request)
    {
        $data = $request->all();
        unset($data['_token']);
        // echo "<pre>"; print_r($data); exit;
        $result = resolve('advisor')->updateAdvisor($adId, $data);
        if ($result) {
            return redirect('/advisor/details/' . $result)->with(['status' => 'success', 'message' => 'Advisor added successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function delete($adId = null)
    {
        $result = resolve('advisor')->deleteAdvisor($adId);
        if ($result) {
            return redirect('/dashboard/advisors')->with(['status' => 'danger', 'message' => 'Advisor deleted successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function change_status(Request $request)
    {
        $data = $request->all();
        $result = resolve('advisor')->changeStatus($data);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    // public function importAdvisors()
    // {
    //     $default_disclaimer = resolve('advisor')->getDefaultDisclaimer();
    //     $dd_id = $default_disclaimer['DisclaimerId'];
    //     $file_path = public_path('csv/advisors_test.csv');
    //     try {
    //         $file = fopen($file_path, 'r');
    //     } catch (\Exception $e) {
    //         echo "File not found";
    //         die;
    //     }
    //     echo "<pre>";
    //     while (($line = fgetcsv($file)) !== FALSE) {
    //         print_r($line);
    //     }
    //     fclose($file);
    // }
}
