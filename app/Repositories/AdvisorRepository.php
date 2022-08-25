<?php

namespace App\Repositories;

use App\Models\Advisor;
use App\Models\Disclaimer;

class AdvisorRepository
{
    public function getAll($paginate = false, $per_page = null)
    {
        // $listing = Advisor::where('IsActive', 1);
        if ($paginate) {
            return Advisor::orderBy('AdvisorId', 'desc')->paginate($per_page);
        }
        return Advisor::orderBy('AdvisorId', 'desc')->get();
    }

    public function getActiveAdvisers($paginate = false, $per_page = null)
    {
        // $listing = Advisor::where('IsActive', 1);
        if ($paginate) {
            return Advisor::where('IsActive', 1)->orderBy('workshop_code', 'ASC')->paginate($per_page);
        }
        return Advisor::where('IsActive', 1)->orderBy('workshop_code', 'ASC')->get();
    }

    public function getAllDisclaimers()
    {
        return Disclaimer::where('IsActive', 1)->get();
    }
    public function addNewAdvisor($data = [])
    {
        if (!empty($data)) {
            $advisorObj = new Advisor();
            $advisorObj->AdvisorName = $data['name'];
            $advisorObj->company_name = $data['company_name'];
            $advisorObj->workshop_code = $data['workshop_code'];
            $advisorObj->AdvisorAddress = $data['address'];
            // $advisorObj->PhoneNumber = $data['phone_number'];
            // $advisorObj->FaxNumber = $data['fax_number'];
            $advisorObj->DefaultDisclaimerId = $data['default_disclaimer'];
            $advisorObj->IsActive = 1;
            $advisorObj->SuppressConfidential = isset($data['suppress_confidential']) ? 1 : 0;
            if ($advisorObj->save()) {
                return $advisorObj->AdvisorId;
            } else {
                return false;
            }
        }
    }

    public function getById($adId = null)
    {
        return Advisor::with('disclaimer')->where('AdvisorId', $adId)->first();
    }

    public function updateAdvisor($adId = null, $data = [])
    {
        if ($adId == null) {
            return false;
        }
        $advisorObj = Advisor::find($adId);
        $advisorObj->AdvisorName = $data['name'];
        $advisorObj->company_name = $data['company_name'];
        $advisorObj->workshop_code = $data['workshop_code'];
        $advisorObj->AdvisorAddress = $data['address'];

        $advisorObj->DefaultDisclaimerId = $data['default_disclaimer'];
        $advisorObj->IsActive = 1;
        $advisorObj->SuppressConfidential = isset($data['suppress_confidential']) ? 1 : 0;
        if ($advisorObj->save()) {
            return $advisorObj->AdvisorId;
        } else {
            return false;
        }
    }

    public function deleteAdvisor($adId = null)
    {
        if ($adId != null) {
            $result = Advisor::where('AdvisorId', $adId)->delete();
            if ($result) {
                return true;
            }
        }
        return false;
    }

    public function changeStatus($data = [])
    {
        if (empty($data)) {
            return false;
        }
        if (trim($data['advisor_id']) == "" || trim($data['action']) == "") {
            return false;
        }
        $advisor = Advisor::find($data['advisor_id']);
        if (!$advisor) {
            return false;
        }
        if ($data['action'] == "active") {
            $advisor->IsActive = 1;
        } elseif ($data['action'] == "deactive") {
            $advisor->isActive = 0;
        } else {
            return false;
        }
        if ($advisor->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function getDefaultDisclaimer()
    {
        $disclaimer = Disclaimer::where('IsDefault', 1)->first();
        if (!$disclaimer) {
            $disclaimer = Disclaimer::where('DisclaimerName', 'Standard')->first();
        }
        return $disclaimer->toArray();
    }
}
