<?php

use Illuminate\Support\Facades\Route;

// Todas las rutas web redirigen a la SPA Vue.js
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '.*');
