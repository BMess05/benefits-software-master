<?php

namespace App\Repositories;

use App\Models\Disclaimer;
use Illuminate\Support\Facades\DB;

class DisclaimerRepository
{
    public function addNewDisclaimers($data = [])
    {
        if (!empty($data)) {
            $discklaimerObj = new Disclaimer();
            $discklaimerObj->DisclaimerName = $data['name'];
            $discklaimerObj->DisclaimerText = $data['disclaimer_text'];
            $discklaimerObj->IsDefault = (isset($data['make_default']) ? $data['make_default'] : 0);
            $discklaimerObj->IsActive = 1;

            if ($discklaimerObj->save()) {
                return $discklaimerObj->DisclaimerId;
            } else {
                return false;
            }
        }
    }

    public function getDisclaimerById($did = null)
    {
        return Disclaimer::with('advisor')->where('DisclaimerId', $did)->first();
    }

    public function updateDisclaimer($empid = null, $data = [])
    {
        if ($empid == null) {
            return false;
        }
        $discklaimerObj = Disclaimer::find($empid);
        $discklaimerObj->DisclaimerName = $data['name'];
        $discklaimerObj->DisclaimerText = $data['disclaimer_text'];
        $discklaimerObj->IsDefault = (isset($data['make_default']) ? $data['make_default'] : 0);
        $discklaimerObj->IsActive = 1;

        if ($discklaimerObj->save()) {
            return $discklaimerObj->DisclaimerId;
        } else {
            return false;
        }
    }

    public function deleteDisclaimer($id = null)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $res = Disclaimer::where('DisclaimerId', $id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return true;
    }
}
