<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TspRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $age_by_end_of_year = $this->get_age_by_end_of_year();
        if ($age_by_end_of_year >= 50) {
            $total_50_or_greater = resolve('applookup')->getValueByAppLookupName('total_allowed_contri_for_age_50_or_greater');
            $reg_max = $total_50_or_greater - $this->get('tsp_contribution_catchup', 0);
            $catchup_max = $total_50_or_greater - $this->get('regular_tsp_contribution', 0);
        } else {
            $total_less_than_50 = resolve('applookup')->getValueByAppLookupName('total_allowed_contri_for_age_less_than_50');
            $reg_max = $total_less_than_50 - $this->get('tsp_contribution_catchup', 0);
            $catchup_max = $total_less_than_50 - $this->get('regular_tsp_contribution', 0);
        }

        return [
            /* 'current_salary' => 'required|numeric', */
            'statement_date' => 'required|date',
            'regular_tsp_contribution' => 'required|numeric|min:0|max:' . $reg_max,
            'tsp_contribution_catchup' => 'required|numeric|min:0|max:' . $catchup_max,
        ];
    }

    public function messages()
    {
        $age_by_end_of_year = $this->get_age_by_end_of_year();
        if ($age_by_end_of_year >= 50) {
            $allowed_total = resolve('applookup')->getValueByAppLookupName('total_allowed_contri_for_age_50_or_greater');
        } else {
            $allowed_total = resolve('applookup')->getValueByAppLookupName('total_allowed_contri_for_age_less_than_50');
        }
        return [
            'regular_tsp_contribution.max' => 'Total of Traditional and Roth Contribution should not exceed ' . $allowed_total,
            'tsp_contribution_catchup.max' => 'Total of Traditional and Roth Contribution should not exceed ' . $allowed_total,
        ];
    }

    public function get_age_by_end_of_year()
    {
        $empId = $this->route()->parameters()['empid'] ?? 0;
        $emp = $emp = resolve('employee')->getById($empId);
        $currentYear = new \DateTime(date('Y') . '-12-31');
        $bDayObj = new \DateTime($emp->eligibility->DateOfBirth);
        $ageDiff = $bDayObj->diff($currentYear);
        return $ageDiff->y;
    }
}
