<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// studentsのCRUDのルーティング
Route::get('/students', 'App\Http\Controllers\StudentController@index');
Route::post('/students', 'App\Http\Controllers\StudentController@store');
Route::get('/students/{student:id}', 'App\Http\Controllers\StudentController@edit');
Route::patch('/students/{student:id}', 'App\Http\Controllers\StudentController@update');
Route::delete('/students/{student:id}', 'App\Http\Controllers\StudentController@delete');


// ログインと新規登録のルーティング
Route::post('/register', 'App\Http\Controllers\AuthController@register');
// Route::post('/login', 'App\Http\Controllers\AuthController@login');
// Route::post('/logout', 'App\Http\Controllers\AuthController@logout');

Route::group(['middleware' => ['web']], function () {
    Route::post('/login', 'App\Http\Controllers\AuthController@login');
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
});

// パスワード再設定
Route::post('/password/reset/request', 'App\Http\Controllers\AuthController@sendPasswordResetEmail')->name('password.reset');
Route::post('/password/reset', 'App\Http\Controllers\AuthController@updatePassword');

Route::post('/password/reset/verify', 'App\Http\Controllers\AuthController@verifyTokenAndEmail');
