<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::middleware('auth')->group(function () {

    Route::view('/', 'empty')->name('home');
    Route::view('szukaj', 'empty')->name('szukaj');

    Route::prefix('zlecenia')->name('zlecenia.')->group(function () {
        Route::get('/', 'ZlecenieController@index')->name('lista');
        Route::get('/{id}', 'ZlecenieController@show')->name('show');
        Route::post('/api/append_notatka/{id}', 'ZlecenieController@apiAppendNotatka')->name('api.append_opis');
    });

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', 'AdminController@users')->name('users');
    });
});
