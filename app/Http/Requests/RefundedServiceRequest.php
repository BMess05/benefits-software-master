<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefundedServiceRequest extends FormRequest
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
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
            'withdrawal' => 'sometimes',
            'redeposit' => 'sometimes',
            'amount_owned' => 'required_with:redeposit, on'
        ];
    }
}
