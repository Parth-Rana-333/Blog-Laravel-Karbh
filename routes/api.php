<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', LoginController::class);
Route::post('register', RegisterController::class);
Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', LogoutController::class);
    Route::get('dashboard', [DashboardController::class, 'getUser']);
    Route::get('get-all-post', [DashboardController::class, 'getAllBlogList']);
    Route::get('get-post-by-id/{slug}', [DashboardController::class, 'getPostByID']);
    
    // course module routes
    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', [PostController::class, 'listPost']);
        Route::post('add-post', [PostController::class, 'addPost']);
        Route::get('edit/{slug}',[PostController::class, 'editPost']);
        Route::post('update-post',[PostController::class, 'updatePost']);
        Route::post('post-delete', [PostController::class, 'deletePost']);
        Route::get('get-category-post', [PostController::class, 'getCategoryList']);
        Route::post('add-comment-post', [PostController::class, 'addComment']);
    });
});
