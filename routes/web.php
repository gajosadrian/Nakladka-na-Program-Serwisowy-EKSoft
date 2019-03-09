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
        Route::get('/{id}', 'ZlecenieController@show')->name('pokaz');

        Route::get('api/get_opis/{id}', 'ZlecenieController@apiGetOpis')->name('api.get_opis');
        Route::post('api/append_notatka/{id}', 'ZlecenieController@apiAppendNotatka')->name('api.append_opis');
        Route::post('api/change_status/{id}', 'ZlecenieController@apiChangeStatus')->name('api.change_status');
    });

    Route::prefix('admin')->middleware('role:super-admin')->group(function () {
        Route::name('admin.')->group(function () {
            Route::get('users', 'AdminController@users')->name('users.lista');
        });

        Route::prefix('rozliczenia')->name('rozliczenia.')->group(function () {
            Route::get('/', 'Rozliczenie\RozliczenieController@index')->name('lista');
            Route::get('/{id}', 'Rozliczenie\RozliczenieController@show')->name('pokaz');
            Route::post('/', 'Rozliczenie\RozliczenieController@store')->name('store');
        });

        Route::prefix('rozliczone_zlecenia')->name('rozliczone_zlecenia.')->group(function () {
            Route::post('/many', 'Rozliczenie\RozliczoneZlecenieController@apiStoreMany')->name('storeMany');
        });
    });
});
