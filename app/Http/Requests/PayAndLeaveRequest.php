<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayAndLeaveRequest extends FormRequest
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
            'current_salary' => 'required|numeric',
            'annual_increase' => 'required|numeric',
            'high3_avg' => 'required|numeric',
            'unusual_sick_leave' => 'sometimes|numeric',
            'unusual_annual_leave' => 'sometimes|numeric',
            // 'csrs_fers_contribution' => 'required|numeric',
            // 'ss_oasdi' => 'required|numeric',
            // 'medicare' => 'required|numeric',
            // 'tax_federal' => 'required|numeric',
            // 'tax_state' => 'required|numeric',
            // 'flexible_spending_account' => 'required|numeric'
        ];
    }
}
