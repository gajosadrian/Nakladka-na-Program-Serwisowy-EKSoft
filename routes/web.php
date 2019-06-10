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

    Route::put('save-field', 'SavedFieldController@update')->name('api.save_field');

    Route::prefix('zlecenia')->name('zlecenia.')->group(function () {
        Route::get('lista', 'ZlecenieController@index')->name('lista');
        Route::get('pokaz/{id}', 'ZlecenieController@show')->name('pokaz');
        Route::get('dla-technika/{technik_id?}/{timestamp?}', 'ZlecenieController@dlaTechnika')->name('dla-technika');
        Route::get('kilometrowka/{technik_id?}/{month_id?}', 'ZlecenieController@kilometrowka')->name('kilometrowka');
        Route::get('wyszukiwanie-czesci/{symbol?}', 'ZlecenieController@wyszukiwanieCzesci')->name('wyszukiwanieCzesci');

        Route::get('api/get_opis/{id}', 'ZlecenieController@apiGetOpis')->name('api.get_opis');
        Route::post('api/append_notatka/{id}', 'ZlecenieController@apiAppendNotatka')->name('api.append_opis');
        Route::post('api/change_status/{id}', 'ZlecenieController@apiChangeStatus')->name('api.change_status');
        Route::post('api/umow_klienta/{id}', 'ZlecenieController@apiUmowKlienta')->name('api.umow_klienta');
        Route::post('api/nie_odbiera/{id}', 'ZlecenieController@apiNieOdbiera')->name('api.nie_odbiera');
        Route::post('api/zatwierdz_blad/{id}', 'ZlecenieController@apiZatwierdzBlad')->name('api.zatwierdz_blad');
        Route::get('api/terminarz_statusy/{technik_id}/{date_string?}', 'ZlecenieController@apiGetTerminarzStatusy')->name('api.terminarz_statusy');
    });

    Route::prefix('admin')->middleware('role:super-admin')->group(function () {
        Route::name('admin.')->group(function () {
            Route::get('users', 'AdminController@users')->name('users.lista');
        });

        Route::prefix('rozliczenia')->name('rozliczenia.')->group(function () {
            Route::get('/', 'Rozliczenie\RozliczenieController@index')->name('lista');
            Route::get('/{id}', 'Rozliczenie\RozliczenieController@show')->name('pokaz');
            Route::get('/{id}/analiza/{zleceniodawca?}', 'Rozliczenie\RozliczenieController@analiza')->name('analiza');
            Route::get('/{id}/hardreload', 'Rozliczenie\RozliczenieController@hardReload')->name('hardreload');
            Route::post('/', 'Rozliczenie\RozliczenieController@store')->name('store');
        });

        Route::prefix('rozliczone_zlecenia')->name('rozliczone_zlecenia.')->group(function () {
            Route::post('many', 'Rozliczenie\RozliczoneZlecenieController@apiStoreMany')->name('storeMany');
            Route::post('destroy', 'Rozliczenie\RozliczoneZlecenieController@apiDestory')->name('destroy');
        });
    });
});
