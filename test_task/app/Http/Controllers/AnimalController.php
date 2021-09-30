<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAnimalRequest;
use App\Http\Response\ResponseHelper;
use App\Models\Field;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    private $fieldModel;

    public function __construct()
    {
        $this->fieldModel = new Field();
    }

    public function create(CreateAnimalRequest $request)
    {
        return ResponseHelper::created(
            $this->fieldModel->addAnimalToField($request->validated()),
            "The animal is added to the field №{$request->validated()->id}"
        );
    }

}
