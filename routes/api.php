<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GoApiController;

Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

Route::middleware('api.token')->group(function () {
    // Original 4 endpoints
    Route::get('/weather', [GoApiController::class, 'getWeather']);
    Route::get('/currency', [GoApiController::class, 'getCurrency']);
    Route::get('/news', [GoApiController::class, 'getNews']);
    Route::post('/data', [GoApiController::class, 'postData']);

    // New 4 Stock Market endpoints
    Route::get('/stock/price', [GoApiController::class, 'getStockPrice']);
    Route::get('/stock/profile', [GoApiController::class, 'getStockProfile']);
    Route::get('/stock/historical', [GoApiController::class, 'getStockHistorical']);
    Route::get('/stock/movers', [GoApiController::class, 'getStockMovers']);
});
