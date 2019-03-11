<?php

namespace App\Http\Controllers\Rozliczenie;

use App\Http\Controllers\Controller;
use App\Models\Rozliczenie\RozliczoneZlecenie;
use App\Models\Zlecenie\Zlecenie;
use Illuminate\Http\Request;

class RozliczoneZlecenieController extends Controller
{
    /**
     * API
     *
     */
    public function apiStoreMany(Request $request)
    {
        $user = auth()->user();

        if (empty($request->zlecenia_ids)) {
            return response()->json('success', 200);
        }

        foreach ($request->zlecenia_ids as $zlecenie_id) {
            $zlecenie = Zlecenie::findOrFail($zlecenie_id);

            $rozliczone_zlecenie = RozliczoneZlecenie::firstOrNew(['zlecenie_id' => $zlecenie->id]);
            $rozliczone_zlecenie->pracownik = $user->pracownik->login;
            $rozliczone_zlecenie->rozliczenie_id = $request->rozliczenie_id;
            $rozliczone_zlecenie->zlecenie_id = $zlecenie->id;
            $rozliczone_zlecenie->zleceniodawca = $zlecenie->zleceniodawca;
            $rozliczone_zlecenie->robocizny = $zlecenie->robocizny;
            $rozliczone_zlecenie->dojazdy = $zlecenie->dojazdy;
            $rozliczone_zlecenie->save();
        }

        return response()->json('success', 200);
    }
}
