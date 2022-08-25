<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FegliRequest extends FormRequest
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
            'salary_override' => 'sometimes|numeric',
            'include_basic' => 'sometimes',
            'basic_amount' => 'required_with:include_basic,on',
            'basicReductionAfterRetirement' => 'required_with:include_basic,on',
            'include_optionA' => 'sometimes',
            'optionA_amount' => 'required_with:include_optionA,on',
            'optionAReductionAfterRetirement' => 'required_with:include_optionA,on',
            'include_optionB' => 'sometimes',
            'b_multiplier' => 'required_with:include_optionB,on',
            'optionB_amount' => 'required_with:include_optionB,on',
            'optionBReductionAfterRetirement' => 'required_with:include_optionB,on',
            'include_optionC' => 'sometimes',
            'optionC_amount' => 'required_with:include_optionC,on',
            'c_multiplier' => 'required_with:include_optionC,on',
            'optionCReductionAfterRetirement' => 'required_with:include_optionC,on',
        ];
    }
}
