<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zlecenie;
use App\Models\Rozliczenie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RozliczenieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        // $rozliczenia = Rozliczenie\Rozliczenie::all();
        $rozliczenia = null;

        return view('rozliczenia.lista', compact(
            'rozliczenia'
        ));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        $zlecenia_nierozliczone = Zlecenie\Zlecenie::where('id_zlecenia', '>', 45000)->withRelations()->oldest('DataPrzyjecia')->get();

        return view('rozliczenia.pokaz', compact(
            'zlecenia_nierozliczone'
        ));
    }
}
