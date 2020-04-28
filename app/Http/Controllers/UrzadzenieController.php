<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zlecenie\Urzadzenie;
use App\Models\Zlecenie\Zlecenie;

class UrzadzenieController extends Controller
{
    public function zdjecia(Request $request)
    {
        $zlecenia = Zlecenie::whereHas('urzadzenie')->latest('id_zlecenia')->paginate(10);
        dd($zlecenia);

        if ($request->wantsJson()) {
            return response()->json([
                'urzadzenia' => Urzadzenie::limit(10)->get()->map->only('id', 'producent', 'nazwa', 'model', 'kod_wyrobu', 'nr_seryjny'),
            ]);
        }

        return view('urzadzenie.zdjecia');
    }
}
