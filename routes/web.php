<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger JSON
Route::get('/api-docs', function () {
    return response()->file(storage_path('api-docs/api-docs.json'));
})->name('l5-swagger.api');

// Swagger UI
Route::get('/api/documentation', function () {
    return view('swagger');
})->name('swagger.ui');

// Redirect
Route::get('/docs', function () {
    return redirect('/api/documentation');
});
