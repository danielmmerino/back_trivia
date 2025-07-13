<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\TokenAuth;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(TokenAuth::class)->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});
