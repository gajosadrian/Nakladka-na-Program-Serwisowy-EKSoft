<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subiekt\Subiekt_Towar as Towar;
use App\Models\Zlecenie\KosztorysPozycja as Pozycja;
use App\Models\Zlecenie\Zlecenie;

class KosztorysController extends Controller
{
    public function updatePozycja(Request $request, int $pozycja)
    {
        $towar = Towar::where('tw_Symbol', $request->symbol)->firstOrFail();

        $pozycja = Pozycja::firstOrCreate([
            'id' => $pozycja,
        ]);

        $naszykowana_czesc_key = $pozycja->naszykowana_czesc_key;

        $pozycja->towar_id = $towar->id;
        $pozycja->opis = $request->opis ?? '';
        $pozycja->cena = $request->cena;
        $pozycja->vat = $request->vat / 100;
        $pozycja->ilosc = $request->ilosc;

        if ($naszykowana_czesc_key) {
            $pozycja->naszykowana_czesc_key = $naszykowana_czesc_key;
        }

        $pozycja->save();

        return response()->json('success');
    }

    public function destroyPozycja(Pozycja $pozycja)
    {
        $pozycja->delete();
        return response()->json('success');
    }

    public function storePozycja(Request $request)
    {
        $zlecenie = Zlecenie::findOrFail($request->zlecenieId);
        $towar = Towar::where('tw_Symbol', $request->symbol)->firstOrFail();

        $zlecenie->kosztorys_pozycje()->create([
            'zlecenie_id' => $zlecenie->id,
            'zlecenie_nr' => $zlecenie->nr,
            'towar_id' => $towar->id,
            'ilosc' => 1,
            'jednostka' => $towar->jednostka,
            'cena' => $towar->cena->netto1,
            'vat' => $towar->vat->value,
        ]);
    }
}
