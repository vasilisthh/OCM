<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SetupController;

// Handle OPTIONS requests for CORS preflight
Route::options('/{any}', function() {
    return response('', 200);
})->where('any', '.*');

Route::get('/ping', function() {
    return response()->json([
        'status' => 'success',
        'message' => 'API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/setup/initialize', [SetupController::class, 'initialize']);

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::post('/fetch', [UserController::class, 'fetch']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);  
});
