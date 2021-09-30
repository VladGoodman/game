<?php

namespace App\Http\Requests;

use App\Http\Response\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

abstract class ApiFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            ResponseHelper::custom($errors, 'Validation error!', JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    abstract public function authorize();
    abstract public function rules();
}
