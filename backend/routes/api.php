<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchesController;
use App\Http\Controllers\Api\CountersController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\StatusesController;
// use App\Http\Controllers\Api\RolesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users', [UsersController::class, 'index']);
    // Route::get('/users/{id}', [UsersController::class, 'show']);
    // Route::post('/users', [UsersController::class, 'store']);
    // Route::put('/users/{id}', [UsersController::class, 'update']);
    // Route::delete('/users/{id}', [UsersController::class, 'destroy']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::get('/users', [UsersController::class, 'index']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::post('/users', [UsersController::class, 'store']);
Route::put('/users/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);

Route::resource('/statuses', StatusesController::class);

Route::resource('/branches', BranchesController::class);

Route::resource('/counters', CountersController::class);
// Route::get('/roles', [RolesController::class, 'index']);
// Route::get('/roles/{id}', [RolesController::class, 'show']);
// Route::post('/roles', [RolesController::class, 'store']);
// Route::put('/roles/{id}', [RolesController::class, 'update']);