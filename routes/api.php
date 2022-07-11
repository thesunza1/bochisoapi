<?php

use App\Http\Controllers\BscController;
use App\Http\Controllers\BscDetailSetIndicatorsController;
use App\Http\Controllers\BscSetIndicatorsController;
use App\Http\Controllers\BscTargetsController;
use App\Http\Controllers\BscTopicsController;
use App\Http\Controllers\BscUnitsController;
use App\Http\Controllers\UserController;
use App\Models\BscDetailSetIndicators;
use App\Models\BscSetIndicators;
use App\Models\BscTargets;
use App\Models\BscTopics;
use App\Models\BscUnits;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('', [UserController::class, 'index']);
    });
    Route::prefix('units')->group(function () {
        Route::post('/', [BscUnitsController::class, 'index']);
        Route::post('/create', [BscUnitsController::class, 'create']);
        Route::post('/update', [BscUnitsController::class, 'update']);
    });
    Route::prefix('topics')->group(function () {
        //topics/create
        Route::post('/create', [BscTopicsController::class, 'create']);
        Route::post('/update', [BscTopicsController::class, 'update']);
        Route::post('/', [BscTopicsController::class, 'index']);
    });
    Route::prefix('targets')->group(function () {
        Route::post('getwitharrtopic', [BscTargetsController::class, 'getWithArrTopic']);
        Route::post('createwithtopic', [BscTargetsController::class, 'createWithTopic']);
        Route::post('createwiththis', [BscTargetsController::class, 'createWithThis']);
        Route::post('getwithtopic', [BscTargetsController::class, 'getWithTopic']);
        Route::post('update', [BscTargetsController::class, 'update']);
        Route::post('', [BscTargetsController::class, 'index']);
    });
    Route::prefix('setindicators')->group(function () {
        Route::post('fastcreate', [BscSetIndicatorsController::class, 'fastCreate']);
        Route::post('', [BscSetIndicatorsController::class, 'index']);
        Route::post('createwitharr', [BscSetIndicatorsController::class, 'createWithTopicArr'] );
        Route::post('update', [BscSetIndicatorsController::class, 'update'] );
    });
    Route::prefix('detailsetindicators')->group(function () {
        Route::post('', [BscDetailSetIndicatorsController::class, 'index']);
        Route::post('update', [BscDetailSetIndicatorsController::class, 'update']);
    });
});
Route::prefix('user')->group(function () {
    Route::post('/login', [UserController::class, 'login']);
});

Route::post('testdate', [BscController::class, 'testdate']);
