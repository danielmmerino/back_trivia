<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\TokenAuth;
use App\Http\Middleware\CheckApiKey;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LevelController;

Route::post('/login', [AuthController::class, 'login'])->middleware(CheckApiKey::class);
Route::post('/login-usuarios', [AuthController::class, 'loginUsuarios'])->middleware(CheckApiKey::class);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

Route::get('/categories', [CategoryController::class, 'index'])
    ->middleware(TokenAuth::class);
Route::get('/niveles', [LevelController::class, 'index'])
    ->middleware(TokenAuth::class);

Route::middleware(TokenAuth::class)->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});
