<?php

namespace App\Http\Controllers;

use App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Terminarz;
use App\Models\Zlecenie\KosztorysPozycja;
use App\Models\SMS\Technik;
// use App\Models\Subiekt\Subiekt_Towar;
use App\Models\Czesc\Naszykowana;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CzesciController extends Controller
{
    public function indexMenu()
    {
        return view('czesci.menu');
    }

    public function indexSzykowanie(int $technik_id = null, string $date_string = null)
    {
        if (! $date_string) {
            $today = today();
            $date = $today->copy();
            if ($today->copy()->addHours(10)->lt(now())) {
                $date->addDay();
            }
            $date_string = $date->toDateString();
        } else {
            $date = Carbon::parse($date_string)->startOfDay();
        }

        $technicy = Technik::getLast();
        $technik = Technik::find($technik_id);

        $terminy = [];
        if ($technik) {
            $terminy = Terminarz::getTerminy($technik->id, $date_string, [
                'do_wyjasnienia' => false,
                'has_zlecenie' => true,
            ]);
        }

        return view('czesci.szykowanie', compact('date', 'date_string', 'technik', 'technicy', 'terminy'));
    }

    public function updateNaszykuj(Request $request, KosztorysPozycja $kosztorys_pozycja)
    {
        $user = $request->user();
        $pozycja = $kosztorys_pozycja;

        $technik_id = $request->technik_id;
        $ilosc = str_replace(',', '.', $request->ilosc);

        $naszykowana_czesc = Naszykowana::firstOrNew([
            'key' => $pozycja->naszykowana_czesc_key,
            'zlecenie_id' => $pozycja->zlecenie_id,
            'towar_id' => $pozycja->towar_id,
        ]);
        if ( ! $ilosc or $ilosc == 0) {
            $naszykowana_czesc->delete();
            return response()->json('deleted', 200);
        }

        $zlecenie = $pozycja->zlecenie;
        if ( ! $zlecenie->terminarz) {
            abort(401);
        }

        $key = strtolower(str_random(4));

        $pozycja->naszykowana_czesc_key = $key;
        $pozycja->save();

        $naszykowana_czesc->key = $key;
        $naszykowana_czesc->user_id = $user->id;
        $naszykowana_czesc->technik_id = $technik_id;
        $naszykowana_czesc->ilosc = $ilosc;
        $naszykowana_czesc->ilosc_do_zwrotu = $ilosc;
        $naszykowana_czesc->zlecenie_data = $zlecenie->terminarz->data_rozpoczecia;
        $naszykowana_czesc->save();

        return response()->json('saved', 200);
    }

    public function updateZamontuj(Request $request, KosztorysPozycja $kosztorys_pozycja, int $towar_id)
    {
        $user = $request->user();
        $pozycja = $kosztorys_pozycja;

        $technik_id = $request->technik_id;
        $ilosc = str_replace(',', '.', $request->ilosc);

        if ( ! $ilosc or $ilosc == 0) {
            abort(401);
        }

        if ( ! $pozycja->naszykowane_czesci) {
            $this->updateNaszykuj($request, $pozycja->zlecenie_id, $towar_id);
            $pozycja->naszykowane_czesci = $pozycja->naszykowane_czesci()->get();
        }

        $naszykowana_czesc = $pozycja->naszykowane_czesci;
        $naszykowana_czesc->ilosc_zamontowane = $ilosc;
        $naszykowana_czesc->ilosc_do_zwrotu -= $ilosc;
        $naszykowana_czesc->save();

        return response()->json('saved', 200);
    }
}
