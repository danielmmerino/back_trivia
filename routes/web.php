<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/fix-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    //Artisan::call('config:cache'); // coméntalo si no usas archivos de configuración personalizados
    return '✔️ Configuración limpiada y cache borrada';
});