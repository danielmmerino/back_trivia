<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\TokenAuth;
use App\Http\Middleware\CheckApiKey;
use App\Http\Controllers\CategoryController;

Route::post('/login', [AuthController::class, 'login'])->middleware(CheckApiKey::class);

Route::middleware(TokenAuth::class)->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/categories', [CategoryController::class, 'index']);
});
