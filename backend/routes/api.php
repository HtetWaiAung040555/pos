<?php

use App\Http\Controllers\Api\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/users', [UsersController::class, 'index']);
//     Route::get('/users/{id}', [UsersController::class, 'show']);
//     Route::post('/user', [UsersController::class, 'store']);
//     Route::put('/users/{id}', [UsersController::class, 'update']);
//     Route::delete('/users/{id}', [UsersController::class, 'destroy']);
// });

Route::get('/users', [UsersController::class, 'index']);
Route::get('/user/{id}', [UsersController::class, 'show']);
Route::post('/user', [UsersController::class, 'store']);
Route::put('/user/{id}', [UsersController::class, 'update']);
Route::delete('/user/{id}', [UsersController::class, 'destroy']);