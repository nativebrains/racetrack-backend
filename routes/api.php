<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['as' => 'v1.', 'prefix' => 'v1'], function () {
    Route::get('get-csrf-token', [\App\Http\Controllers\API\V1\HomeController::class, 'projectMetaData'])->name('project.metaData');
    Route::get('meta-data', [\App\Http\Controllers\API\V1\HomeController::class, 'projectMetaData'])->name('project.metaData');
    Route::get('race', [\App\Http\Controllers\API\V1\HomeController::class, 'fetchRaceData'])->name('fetch.race.data');
});
