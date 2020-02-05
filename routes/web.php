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
        Route::get('mobile-app', 'ZlecenieController@mobileApp')->name('mobileApp');
        Route::get('lista', 'ZlecenieController@index')->name('lista');
        Route::get('pokaz/{id}', 'ZlecenieController@show')->name('pokaz');
        Route::get('pokaz/{zlecenie_id}/zdjecia', 'ZlecenieZdjecieController@show')->name('pokazZdjecia');
        Route::get('dla-technika/{technik_id?}/{timestamp?}', 'ZlecenieController@dlaTechnika')->name('dla-technika');
        Route::get('planowanie-trasy/{technik_id?}/{date_string?}', 'ZlecenieController@planowanieTrasy')->name('planowanieTrasy');
        Route::get('kilometrowka/{technik_id?}/{month_id?}', 'ZlecenieController@kilometrowka')->name('kilometrowka');
        Route::get('wyszukiwanie-zlecenia/{nr_zlec?}', 'ZlecenieController@wyszukiwanieZlecenia')->name('wyszukiwanieZlecenia');
        Route::get('wyszukiwanie-czesci/{symbol?}', 'ZlecenieController@wyszukiwanieCzesci')->name('wyszukiwanieCzesci');
        Route::get('menu-czesci', 'CzesciController@indexMenu')->name('indexMenu');
        Route::get('logs/{technik_id?}/{date_string?}', 'ZlecenieController@logs')->name('logs');

        Route::get('api/get_opis/{id}', 'ZlecenieController@apiGetOpis')->name('api.get_opis');
        Route::post('api/append_notatka/{id}', 'ZlecenieController@apiAppendNotatka')->name('api.append_opis');
        Route::post('api/change_status/{id}', 'ZlecenieController@apiChangeStatus')->name('api.change_status');
        Route::post('api/umow_klienta/{id}', 'ZlecenieController@apiUmowKlienta')->name('api.umow_klienta');
        Route::post('api/nie_odbiera/{id}', 'ZlecenieController@apiNieOdbiera')->name('api.nie_odbiera');
        Route::post('api/zatwierdz_blad/{id}', 'ZlecenieController@apiZatwierdzBlad')->name('api.zatwierdz_blad');
        Route::get('api/terminarz_statusy/{technik_id}/{date_string?}', 'ZlecenieController@apiGetTerminarzStatusy')->name('api.terminarz_statusy');
        Route::delete('api/remove-status/{status_id}', 'ZlecenieController@apiRemoveStatus')->name('api.removeStatus');
        Route::get('api/get-from-terminarz/{date_string?}', 'ZlecenieController@apiGetFromTerminarz')->name('api.getFromTerminarz');
    });

    Route::prefix('zlecenie-zdjecie')->name('zlecenie-zdjecie.')->group(function () {
        Route::get('{zdjecie}', 'ZlecenieZdjecieController@make')->name('make');
        Route::post('/', 'ZlecenieZdjecieController@store')->name('store');
        Route::delete('/{zdjecie}', 'ZlecenieZdjecieController@destroy')->name('destroy');
    });

    Route::prefix('czesci')->name('czesci.')->group(function () {
        Route::get('szykowanie/{technik_id?}/{date_string?}', 'CzesciController@indexSzykowanie')->name('indexSzykowanie');
        Route::patch('naszykuj/{zlecenie_id}/{towar_id}', 'CzesciController@updateNaszykuj')->name('updateNaszykuj');
        Route::get('odbior/{technik_id?}', 'CzesciController@indexOdbior')->name('indexOdbior');
        Route::get('dodawanie/{technik_id?}/{date_string?}', 'CzesciController@indexDodawanie')->name('indexDodawanie');
    });

    Route::prefix('inwentaryzacja')->name('inwentaryzacja.')->group(function () {
        Route::get('show', 'InwentaryzacjaController@show')->name('show');
        Route::put('/{symbol}', 'InwentaryzacjaController@update')->name('update');
        Route::get('not-checked', 'InwentaryzacjaController@showNotChecked')->name('showNotChecked');
        Route::get('summary/{mode?}', 'InwentaryzacjaController@summary')->name('summary');
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

Route::get('zdjecie-towaru/{id}', function (string $id) {
    app('debugbar')->disable();
    $towar = App\Models\Subiekt\Subiekt_Towar::findOrFail($id);
    return response()->make($towar->zdjecie_binary)->header('Content-Type', 'image/png');
})->name('zdjecie_towaru');
