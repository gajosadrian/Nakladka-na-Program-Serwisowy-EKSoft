<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subiekt\Subiekt_Towar as Towar;
use App\Models\Zlecenie\KosztorysPozycja as Pozycja;

class KosztorysController extends Controller
{
    public function updatePozycja(Request $request)
    {
        $towar = Towar::where('tw_Symbol', $request->symbol)->firstOrFail();

        $pozycja = Pozycja::firstOrCreate([
            'id' => $request->id,
        ]);

        $pozycja->towar_id = $towar->id;
        $pozycja->opis = $request->opis ?? '';
        $pozycja->cena = $request->cena;
        $pozycja->vat = $request->vat / 100;
        $pozycja->ilosc = $request->ilosc;
        $pozycja->save();

        return response()->json('success');
    }
}
