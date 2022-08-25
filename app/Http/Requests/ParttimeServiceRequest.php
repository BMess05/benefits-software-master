<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParttimeServiceRequest extends FormRequest
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
            // 'from_date' => 'sometimes|date',
            // 'to_date' => 'sometimes|date|after:from_date',
            // 'hours_weekly' => 'sometimes|numeric',
            // 'percentage' => 'sometimes|numeric'
        ];
    }
}
