<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => 'auth:api'], function () {

    Route::apiResource('boards', BoardController::class)
        ->only(['index']);

    Route::apiResource('columns', ColumnController::class)
        ->only(['index', 'store', 'destroy']);

    Route::post('/reset-cards-order', [CardController::class, 'resetCardsOrder']);

    Route::apiResource('cards', CardController::class)
        ->only(['index', 'store', 'update']);

});




