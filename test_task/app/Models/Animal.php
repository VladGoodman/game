<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    const ID_TYPE_ANIMAL_IS_HARE = 1;
    const ID_TYPE_ANIMAL_IS_WOLF = 2;

    public $timestamps = false;
    protected $fillable = ['coordinate_x', 'coordinate_y', 'field_id', 'type_id', 'moves'];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'id');
    }


    private function haresNextToWolf($wolfs, $hares)
    {
        $wolfs_coordinates = [];
        $id_array_to_delete = [];
        foreach ($wolfs as $wolf) {
            array_push($wolfs_coordinates, $wolf->only(['coordinate_x', 'coordinate_y', 'id']));
        }
        foreach ($wolfs_coordinates as $coordinate) {
            foreach ($hares as $hare) {
                $filed_x = collect($hare)->whereBetween('coordinate_x', [($coordinate['coordinate_x'] - 1), ($coordinate['coordinate_x'] + 1)])->all();
                $filed_y = collect($hare)->whereBetween('coordinate_y', [($coordinate['coordinate_y'] - 1), ($coordinate['coordinate_y'] + 1)])->all();
                if (($filed_x and $filed_y) and ($filed_x === $filed_y)) {
                    array_push($id_array_to_delete, [$coordinate['id'] => $hare['id']]);
                }
            }
        }
        self::whereIn('id', collect($id_array_to_delete)->duplicates()->filter(function ($value, $key) {
            return $key >= 2;
        })->all())->delete();
    }

    private function haresInSameCageWithWolf($wolfs, $hares)
    {
        $wolfs_coordinates = [];
        $id_array_to_delete = [];
        foreach ($wolfs as $wolf) {
            array_push($wolfs_coordinates, $wolf->only(['coordinate_x', 'coordinate_y']));
        }
        foreach ($hares as $hare) {
            if (in_array([
                'coordinate_x' => $hare['coordinate_x'],
                'coordinate_y' => $hare['coordinate_y']
            ], $wolfs_coordinates)) {
                array_push($id_array_to_delete, $hare['id']);
            }
        }
        self::whereIn('id', $id_array_to_delete)->delete();
    }


    private function checkLocationOfHares($items)
    {
        $items = collect($items);
        $wolfs = $items->where('type_id', self::ID_TYPE_ANIMAL_IS_WOLF)->values()->keyBy('id');
        $hares = $items->where('type_id', self::ID_TYPE_ANIMAL_IS_HARE)->values()->keyBy('id');
        $this->haresInSameCageWithWolf($wolfs, $hares);
        $this->haresNextToWolf($wolfs, $hares);
    }

    public function checkLocations($items)
    {
        $this->checkLocationOfHares($items);
    }
}
