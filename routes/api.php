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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
});

Route::group(['prefix' => 'products'], function () {
    Route::get('/', 'ProductController@products');
    Route::get('/{id}', 'ProductController@product');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::delete('/{id}', 'ProductController@destroy');
        Route::put('/{id}', 'ProductController@edit');
        Route::post('/', 'ProductController@store');
    });
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', 'CategoryController@categories');
    Route::get('/{id}', 'CategoryController@category');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::delete('/{id}', 'CategoryController@destroy');
        Route::put('/{id}', 'CategoryController@edit');
        Route::post('/', 'CategoryController@store');
    });
});
