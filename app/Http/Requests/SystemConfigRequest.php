<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemConfigRequest extends FormRequest
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
        return [
            'csrs_cola' => 'required|numeric',
            'fers_cola' => 'required|numeric',
            'sal_increase' => 'required|numeric',
            'ss_cola' => 'required|numeric',
            'pia_formula_fb' => 'required|numeric',
            'income_limit' => 'required|numeric',
            'avg_prem_increase' => 'required|numeric',
            'deff_limit' => 'required|numeric',
            'catchup_limit' => 'required|numeric',
            'gfund' => 'required|numeric',
            'ffund' => 'required|numeric',
            'cfund' => 'required|numeric',
            'sfund' => 'required|numeric',
            'ifund' => 'required|numeric',
        ];
    }
}
