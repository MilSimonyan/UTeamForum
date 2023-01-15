<?php

use App\Http\Controllers\PostController;
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

Route::middleware('auth:sso')->controller(PostController::class)
    ->prefix('/post')
    ->group(function () {
        Route::GET('/', 'index');
        Route::POST('/', 'store');
        Route::GET('/{id}', 'show');
        Route::DELETE('/{id}', 'destroy');
    });

Route::middleware('auth:sso')->get('/user', function (){
    return new \Illuminate\Http\JsonResponse(\Illuminate\Support\Facades\Auth::user());
});
