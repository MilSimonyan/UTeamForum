<?php

use App\Http\Controllers\PostController;
use GuzzleHttp\Client;
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

Route::middleware('auth')->controller(PostController::class)
    ->prefix('/post')
    ->group(function () {
        Route::GET('/', 'index');
        Route::POST('/', 'store');
        Route::GET('/{id}', 'show');
        Route::DELETE('/{id}', 'destroy');
    });

