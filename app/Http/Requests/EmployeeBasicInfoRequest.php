<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class EmployeeBasicInfoRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        $rules = [
            'name' => 'required',
            'advisor' => 'required',
            'date_received' => 'required',
            'due_date' => 'required',
            'report_month' => 'required',
            'report_year' => 'required',
            'system' => 'required', // |in:4,5,6
            'retirement_type' => 'required',
            'employee_type' => 'required',
            'other_employee_type' => 'required_if:employee_type,12|in:13,14,15,0',
            'marital_status' => 'required',
            // 'spouse_name' => 'required_if:marital_status,16'
        ];
        // 'required|in:7,8,9,10',
        return $rules;
    }

    public function messages()
    {
        return [
            'other_employee_type.required_if' => 'If Emplloyee Type is Special Provision, Other type is required.'
        ];
    }
}
