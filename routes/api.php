<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoApiController;

Route::middleware('passport')->group(function () {
    Route::get('/weather', [GoApiController::class, 'getWeather']);
    Route::get('/currency', [GoApiController::class, 'getCurrency']);
    Route::get('/news', [GoApiController::class, 'getNews']);
    Route::post('/data', [GoApiController::class, 'postData']);
});
