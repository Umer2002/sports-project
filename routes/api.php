<?php

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

// Example route
Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

// Synchronisation with PlayTube
use App\Http\Controllers\PlaytubeSyncController;

Route::post('/playtube/video', [PlaytubeSyncController::class, 'storeFromPlaytube']);
Route::post('/laravel/video', [PlaytubeSyncController::class, 'storeFromLaravel'])->middleware('auth:sanctum');
