<?php

namespace App\Http\Requests;


class CreateAnimalRequest extends ApiFormRequest
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
            'id' => 'required|integer',
            'coordinate_x' => 'required|integer',
            'coordinate_y' => 'required|integer',
            'type_id' => 'required|integer|exists:type_animals,id'
        ];
    }

    public function all($keys = null)
    {
        return array_merge(parent::all(), $this->route()->parameters());
    }
}
