<?php

use Illuminate\Support\Facades\Route;

// Frontend simples de estudo - HTML + JS puro, sem build step.
// Ele consome a mesma API REST via fetch(), a partir do navegador.
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');
