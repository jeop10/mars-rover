<?php

use App\Http\Controllers\SystemController;
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

Route::get('initial-coordinates', [SystemController::class, 'getCoordinates']);
Route::get('previous-movements', [SystemController::class, 'getPreviousMovements']);

Route::post('set-coordinates', [SystemController::class, 'setCoordinates']);
Route::post('send-command', [SystemController::class, 'sendCommand']);
Route::post('clear-navigation', [SystemController::class, 'clearNavigationData']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
