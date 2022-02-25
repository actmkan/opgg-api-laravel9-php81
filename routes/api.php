<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TalkController;
use Illuminate\Support\Facades\Route;

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


/**
 * Auth
 */
Route::prefix('auth')->group(function (){
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function (){
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});

/**
 * Image
 */
Route::prefix('image')->group(function (){
    Route::middleware('auth:sanctum')->group(function (){
        Route::get('/hash', [ImageController::class, 'checkHash']);
        Route::post('/upload', [ImageController::class, 'upload']);
    });
});

/**
 * Talk
 */
Route::prefix('talks')->group(function (){
    Route::get('/', [TalkController::class, 'getTalks']);
    Route::get('/{id}', [TalkController::class, 'getTalk']);
    Route::get('/{id}/channels', [TalkController::class, 'getChannels']);
    Route::get('/{talkId}/channels/{channelId}/articles', [TalkController::class, 'getArticles']);
    Route::middleware('auth:sanctum')->group(function (){
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
