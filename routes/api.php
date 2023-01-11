<?php

use App\Http\Controllers\TagController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::controller(TagController::class)
    ->prefix('/tag')
    ->group(function () {
        Route::get('/get', 'index');
        Route::get('/get/{id}', 'show');
        Route::post('/create', 'store');
        Route::patch('/edit/{id}', 'update');
        Route::delete('/delete/{id}', 'destroy');
    });