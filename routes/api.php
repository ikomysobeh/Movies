<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [\App\Http\Controllers\UserController::class, 'Register']);
Route::post('/login', [\App\Http\Controllers\UserController::class, 'Login']);
Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('/user')->group(function () {
        Route::post('/logout', [\App\Http\Controllers\UserController::class, 'logout'])->middleware('role:admin');
        Route::post('/ChangPassword', [\App\Http\Controllers\UserController::class, 'ChangPassword'])->middleware('role:admin');
        Route::get('/profile', [\App\Http\Controllers\UserController::class, 'profile'])->middleware('role:admin');
        Route::get('/add-permission', [\App\Http\Controllers\UserController::class, 'addPermission'])->middleware('role:admin');
        Route::get('/delete_permission', [\App\Http\Controllers\UserController::class, 'deletePermission'])->middleware('role:admin');
        Route::get('/edit-role', [\App\Http\Controllers\UserController::class, 'editRole'])->middleware('role:admin');
        Route::get('/view-permission', [\App\Http\Controllers\UserController::class, 'viewPermission'])->middleware('role:admin');
        Route::get('/view/{uniqueId?}', [\App\Http\Controllers\UserController::class, 'view'])->middleware('role:admin');
    });


    Route::prefix('/category')->group(function () {
        Route::post('/add', [\App\Http\Controllers\CategoriesController::class, 'Add'])->middleware('role:employee');
        Route::post('/edit', [\App\Http\Controllers\CategoriesController::class, 'edit'])->middleware('role:employee');
        Route::get('/view/{id?}', [\App\Http\Controllers\CategoriesController::class, 'view'])->middleware('role:visitor');
        Route::any('/delete', [\App\Http\Controllers\CategoriesController::class, 'delete'])->middleware('role:employee');

    });
    Route::prefix('/movie')->group(function () {
        Route::post('/add', [\App\Http\Controllers\MovieController::class, 'Add'])->middleware('role:employee');
        Route::post('/edit', [\App\Http\Controllers\MovieController::class, 'edit'])->middleware('role:employee');
        Route::get('/view{uniqueId?}', [\App\Http\Controllers\MovieController::class, 'view'])->middleware('role:visitor');
        Route::any('/delete', [\App\Http\Controllers\MovieController::class, 'delete'])->middleware('role:employee');

    });
    Route::prefix('/rating')->group(function () {
        Route::post('/add', [\App\Http\Controllers\RatingController::class, 'Add'])->middleware('role:visitor');
        Route::post('/edit', [\App\Http\Controllers\RatingController::class, 'edit'])->middleware('role:visitor');
        Route::get('/view', [\App\Http\Controllers\RatingController::class, 'view'])->middleware('role:employee');
        Route::any('/delete', [\App\Http\Controllers\RatingController::class, 'delete'])->middleware('role:visitor');

    });
    Route::prefix('/video')->group(function () {
        Route::post('/add', [\App\Http\Controllers\VideoController::class, 'Add'])->middleware('role:employee');
        Route::post('/edit', [\App\Http\Controllers\VideoController::class, 'edit'])->middleware('role:employee');
        Route::get('/view', [\App\Http\Controllers\VideoController::class, 'view'])->middleware('role:visitor');
        Route::any('/delete', [\App\Http\Controllers\VideoController::class, 'delete'])->middleware('role:employee');

    });
});
