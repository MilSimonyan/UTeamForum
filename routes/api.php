<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentRateController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionLikeController;
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
        Route::POST('/', 'store')->middleware('can:store_post');
        Route::POST('/{id}', 'update')->middleware('can:update_post');
        Route::GET('/{id}', 'show')->middleware('can:show_post');
        Route::DELETE('/{id}', 'destroy')->middleware('can:destroy_post');
    });

Route::middleware('auth:sso')->controller(PostLikeController::class)
    ->prefix('/post-like')
    ->group(function () {
        Route::PUT('/', 'update')->middleware('can:like_post');
    });

Route::middleware('auth:sso')->controller(QuestionController::class)
    ->prefix('/question')
    ->group(function () {
        Route::GET('/', 'index');
        Route::GET('/{id}', 'show')->middleware('can:show_question');
        Route::GET('/{id}/comments', 'comments')->middleware('can:show_comments')->name('questionComments');
        Route::POST('/', 'store')->middleware('can:store_question');
        Route::POST('/{id}', 'update')->middleware('can:update_question');
        Route::DELETE('/{id}', 'destroy')->middleware('can:destroy_question');
    });

Route::middleware('auth:sso')->controller(QuestionLikeController::class)
    ->prefix('/question-like')
    ->group(function () {
        Route::PUT('/', 'update')->middleware('can:like_question');
    });

Route::middleware('auth:sso')->controller(CommentController::class)
    ->prefix('/comment')
    ->group(function () {
        Route::POST('/', 'store')->middleware('can:store_comment');
        Route::POST('/{id}', 'update')->middleware('can:update_comment');
        Route::DELETE('/{id}', 'destroy')->middleware('can:destroy_comment');
    });

Route::middleware('auth:sso')->controller(CommentRateController::class)
    ->prefix('/comment-rate')
    ->group(function () {
        Route::PUT('/', 'update')->middleware('can:rate_comment');
    });

Route::middleware('auth:sso')->controller(TagController::class)
    ->prefix('/tag')
    ->group(function () {
        Route::GET('/', 'index');
        Route::GET('/{id}', 'show');
        Route::POST('/', 'store');
        Route::PATCH('/{id}', 'update');
        Route::DELETE('/{id}', 'destroy');
    });

Route::middleware('auth:sso')->get('/user', function () {
    return new \Illuminate\Http\JsonResponse(\Illuminate\Support\Facades\Auth::user());
});
