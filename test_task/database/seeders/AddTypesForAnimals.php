<?php

namespace Database\Seeders;

use App\Models\TypeAnimal;
use Illuminate\Database\Seeder;

class AddTypesForAnimals extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeAnimal::insert([
            ['name' => 'Заяц'],
            ['name' => 'Волк']
        ]);
    }
}
