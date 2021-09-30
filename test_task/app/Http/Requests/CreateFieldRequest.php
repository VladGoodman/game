<?php

namespace App\Http\Requests;

class CreateFieldRequest extends ApiFormRequest
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
            'coordinate_x' => 'required|integer|min:5|max:100',
            'coordinate_y' => 'required|integer|min:5|max:100',
            'moves' => 'required|integer|min:5|max:20',
        ];
    }
}
