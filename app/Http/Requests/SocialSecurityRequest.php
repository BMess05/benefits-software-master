<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Employee;

class SocialSecurityRequest extends FormRequest
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
        $system_type = $this->getEmployeeDetails($this->empid);
        $rules = [
            'monthly_social_security' => 'required|numeric',
            'social_security_start_age_year' => 'required|numeric',
            'social_security_start_age_month' => 'required|numeric',
            'monthly_social_security_at_start_age' => 'required|numeric',
            'ss_substantial_earning_years' => 'required|numeric',
            'social_security_at_age_of_retirement' => 'numeric|nullable'
        ];
        if ($system_type == 'CSRS Offset') {
            $rules += [
                'social_security_at_age_of_retirement' => 'required|numeric'
            ];
        }
        return $rules;
    }

    protected function getEmployeeDetails($empid)
    {
        $employee = Employee::find($empid);
        if ($employee) {
            return $employee->SystemType;
        }
        return null;
    }
}
