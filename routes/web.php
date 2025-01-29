<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'add']);
Route::delete('/delete/{name}', [TaskController::class, 'delete']);
Route::put('/toggle-completed/{id}', [TaskController::class, 'toggleCompleted']);

