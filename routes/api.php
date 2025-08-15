<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Anthropic API Assistant Routes
Route::prefix('assistant')->group(function () {
    Route::get('/test', 'ApiAssistantController@testConnection');
    Route::post('/message', 'ApiAssistantController@sendMessage');
    Route::post('/drug-info', 'ApiAssistantController@getDrugInfo');
    Route::post('/drug-interactions', 'ApiAssistantController@checkDrugInteractions');
    Route::post('/pharmacy-question', 'ApiAssistantController@askPharmacyQuestion');
});