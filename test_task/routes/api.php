<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\FieldController;
use Illuminate\Support\Facades\Route;

Route::prefix('game')->group(function () {
    Route::post('', [FieldController::class, 'create']);
    Route::get('{id}', [FieldController::class, 'getAnimalsInfo']);
    Route::post('{id}/create', [FieldController::class, 'addAnimal']);
    Route::post('{id}/random', [FieldController::class, 'addRandomAnimals']);
    Route::get('{id}/move', [FieldController::class, 'move']);
});


