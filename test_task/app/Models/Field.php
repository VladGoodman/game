<?php

namespace App\Models;

use App\Http\Requests\ApiFormRequest;
use App\Http\Requests\GetAnimalInformationRequest;
use App\Http\Response\ResponseHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;

class Field extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['coordinate_x', 'coordinate_y', 'moves'];

    public function animals()
    {
        return $this->hasMany(Animal::class, 'field_id', 'id');
    }

    private function getHeightValue()
    {
        return $this->coordinate_y + 2;
    }

    private function getWidthValue()
    {
        return $this->coordinate_x + 2;
    }

    private function checkFreeNumberOfCells(int $number)
    {
        return $number <= (($this->getHeightValue() * $this->getWidthValue()) - $this->animals()->count());
    }

    private function verificationCoordinates(int $coordinate_x, int $coordinate_y)
    {

        if ($this->coordinate_x < $coordinate_x or $coordinate_x < 0) {
            return ResponseHelper::custom(null, 'X coordinate is out of field!', 422);
        } elseif ($this->coordinate_y < $coordinate_y or $coordinate_y < 0) {
            return ResponseHelper::custom(null, 'Y coordinate is out of field!', 422);
        }
        return true;
    }

    public function addAnimalToField(array $information_to_create)
    {
        $field = self::find($information_to_create['id']);
        if (!$field) {
            return ResponseHelper::notFound('Field not found!');
        } elseif (
            $field->verificationCoordinates(
                $information_to_create['coordinate_x'],
                $information_to_create['coordinate_y'])
            !== true
        ) {
            return $field->verificationCoordinates(
                $information_to_create['coordinate_x'],
                $information_to_create['coordinate_y']);
        }

        return ResponseHelper::created(
            $field->animals()->create($information_to_create),
            "The animal is added to the field №{$information_to_create['id']}"
        );
    }


    private function createMultipleAnimals(int $numbers, int $type_animal)
    {
        $max_x = (int)$this->coordinate_x;
        $max_y = (int)$this->coordinate_y;
        $id_field = $this->id;
        $result_array = [];
        for ($i = 0; $i <= $numbers; $i++) {
            array_push($result_array, [
                'coordinate_x' => random_int(0, $max_x),
                'coordinate_y' => random_int(0, $max_y),
                'field_id' => $id_field,
                'type_id' => $type_animal
            ]);
        }
        return $result_array;
    }

    public function addMultipleAnimalsToGame($information_to_create)
    {
        $field = self::find($information_to_create['id']);
        if (!$field) {
            return ResponseHelper::notFound('Field not found!');
        }
        $number_of_animals = $information_to_create['number'];
        $id_types_animals = $information_to_create['type_id'];
        if (!$field->checkFreeNumberOfCells($number_of_animals)) {
            return ResponseHelper::dataError('There is no free space on the field!');
        }
        return ResponseHelper::success(
            $field->animals()->insert(
                $field->createMultipleAnimals($number_of_animals, $id_types_animals)
            ),
            "$number_of_animals animals added to field №{$information_to_create['id']}"
        );
    }

    private function checkXCoordinate($value)
    {
        if($value < 0){
            return 0;
        }
        if($value > $this->coordinate_x){
            return $this->coordinate_x;
        }
        return $value;
    }

    private function checkYCoordinate($value)
    {
        if($value < 0){
            return 0;
        }
        if($value > $this->coordinate_y){
            return $this->coordinate_y;
        }
        return $value;
    }


    public function getAnimalInformation($id)
    {
        $field = self::find($id);
        if (!$field) {
            return ResponseHelper::notFound('Field not found!');
        }
        return ResponseHelper::success($field->animals, 'Success!');
    }

    public function makeMove($id)
    {
        $field = self::find($id);
        if (!$field) {
            return ResponseHelper::notFound('Field not found!');
        }
        $animalModel = new Animal();
        $animals = $field->animals()->get();
        $animalModel::upsert(
            collect($animals)->map(function ($item) use ($field) {
                $range_value = [-1, 1];
                $random_coordinate = random_int(0, 1);
                $random_coordinate_value = $range_value[array_rand($range_value)];
                $filed_x = $random_coordinate ? $field->checkXCoordinate($item->coordinate_x + ($random_coordinate_value)) : $item->coordinate_x;
                $filed_y = $random_coordinate ? $item->coordinate_y : $field->checkYCoordinate($item->coordinate_y + ($random_coordinate_value));
                return [
                    'id' => $item->id,
                    'coordinate_y' => $filed_y,
                    'coordinate_x' => $filed_x,
                    'field_id' => $item->field_id,
                    'type_id' => $item->type_id,
                    'moves' => $item->moves += 1,
                ];
            })->toArray(),
            ['id'],
            ['coordinate_y', 'coordinate_x', 'moves'],
        );

        $animalModel->checkLocations($animals);
        return ResponseHelper::success($animals, 'Success!');
    }

}
