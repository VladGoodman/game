<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAnimalRequest;
use App\Http\Requests\CreateFieldRequest;
use App\Http\Requests\CreateRandomAnimalsRequest;
use App\Http\Requests\GetAnimalInformationRequest;
use App\Http\Requests\MakeMoveRequest;
use App\Http\Response\ResponseHelper;
use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    private $fieldModel;

    public function __construct()
    {
        $this->fieldModel = new Field();
    }

    public function create(CreateFieldRequest $request)
    {
        return ResponseHelper::created(
            $this->fieldModel->create($request->validated()),
            'The game was created!'
        );
    }

    public function addAnimal(CreateAnimalRequest $request)
    {
        return $this->fieldModel->addAnimalToField($request->validated());
    }

    public function addRandomAnimals(CreateRandomAnimalsRequest $request)
    {
        return $this->fieldModel->addMultipleAnimalsToGame($request->validated());
    }

    public function getAnimalsInfo(GetAnimalInformationRequest $request)
    {
        return $this->fieldModel->getAnimalInformation($request->validated()['id']);
    }

    public function move(MakeMoveRequest $request)
    {
        return $this->fieldModel->makeMove($request->validated()['id']);
    }
}
