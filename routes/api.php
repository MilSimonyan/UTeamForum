<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TagController;
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
        Route::POST('/', 'store')->middleware( 'can:store_post');
        Route::PUT('/', 'update')->middleware( 'can:store_post');
        Route::GET('/{id}', 'show')->middleware( 'can:show_post');
        Route::DELETE('/{id}', 'destroy')->middleware('can:destroy_post');
    });

Route::middleware('auth:sso')->controller(QuestionController::class)
    ->prefix('/question')
    ->group(function () {
        Route::GET('/', 'index');
        Route::POST('/', 'store')->middleware( 'can:store_question');
        Route::PUT('/', 'update')->middleware( 'can:store_post');
        Route::GET('/{id}', 'show')->middleware( 'can:show_question');
        Route::DELETE('/{id}', 'destroy')->middleware('can:destroy_question');
    });


Route::middleware('auth:sso')->controller(TagController::class)
    ->prefix('/tag')
    ->group(function () {
        Route::GET('/', 'index');
        Route::GET('//{id}', 'show');
        Route::POST('/', 'store');
        Route::PATCH('/{id}', 'update');
        Route::DELETE('/{id}', 'destroy');
    });

Route::middleware('auth:sso')->get('/user', function () {
    return new \Illuminate\Http\JsonResponse(\Illuminate\Support\Facades\Auth::user());
});
