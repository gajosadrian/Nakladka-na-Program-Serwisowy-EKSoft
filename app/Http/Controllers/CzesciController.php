<?php

namespace App\Http\Controllers;

use App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Terminarz;
use App\Models\Zlecenie\KosztorysPozycja;
use App\Models\SMS\Technik;
use App\Models\Czesc\Naszykowana;
use App\Models\Subiekt\Subiekt_Towar;
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

        $zlecenie = $pozycja->zlecenie;
        if ( ! $zlecenie->terminarz) {
            abort(401);
        }

        $key = $pozycja->naszykowana_czesc_key ?? strtolower(str_random(4));
        $naszykowana_czesc = $pozycja->naszykowane_czesci()->firstOrNew([
            'key' => $key,
        ]);

        if ( ! $ilosc or $ilosc == 0) {
            $naszykowana_czesc->delete();
            return response()->json('deleted', 200);
        }

        $naszykowana_czesc->sprawdzone_at = null;
        $naszykowana_czesc->sprawdzil_user_id = null;
        $naszykowana_czesc->user_id = $user->id;
        $naszykowana_czesc->technik_id = $technik_id;
        $naszykowana_czesc->ilosc = $ilosc;
        $naszykowana_czesc->ilosc_do_zwrotu = $ilosc;
        $naszykowana_czesc->zlecenie_data = $zlecenie->terminarz->data_rozpoczecia;

        if ($pozycja->is_czesc_symbol and trim($pozycja->opis)) {
            $naszykowana_czesc->opis = $pozycja->opis;
        }

        $naszykowana_czesc->save();

        if ($pozycja->naszykowana_czesc_key != $key) {
            $pozycja->naszykowana_czesc_key = $key;
            $pozycja->save();
        }

        return response()->json('saved', 200);
    }

    public function updateZamontuj(Request $request, KosztorysPozycja $kosztorys_pozycja)
    {
        $user = $request->user();
        $pozycja = $kosztorys_pozycja;

        $technik_id = $request->technik_id;
        $ilosc = str_replace(',', '.', $request->ilosc);

        // if ( ! $ilosc or $ilosc == 0) {
        //     $request->type = 'niezamontowane';
        // }

        if ( ! $pozycja->naszykowana_czesc and $request->type == 'niezamontowane') {
            $pozycja->opis = '(-' . $pozycja->ilosc . ') niepotrzebne';
            $pozycja->ilosc = 0;
            $pozycja->save();
            return response()->json('saved', 200);
        }

        if ( ! $pozycja->naszykowana_czesc) {
            $this->updateNaszykuj($request, $pozycja);
            $pozycja->load('naszykowane_czesci');
        }

        $naszykowana_czesc = $pozycja->naszykowana_czesc;
        switch ($request->type) {
            case 'rozpisane':
                $naszykowana_czesc->ilosc_zamontowane = 0;
                $naszykowana_czesc->ilosc_rozpisane = $ilosc;
                $naszykowana_czesc->ilosc_do_zwrotu = $naszykowana_czesc->ilosc;
                $pozycja->opis = 'rozpisane';
                $pozycja->ilosc = $ilosc;
                break;

            case 'niezamontowane':
                $naszykowana_czesc->ilosc_zamontowane = 0;
                $naszykowana_czesc->ilosc_rozpisane = 0;
                $naszykowana_czesc->ilosc_do_zwrotu = $naszykowana_czesc->ilosc;
                $pozycja->opis = '(-' . $naszykowana_czesc->ilosc . ') niepotrzebne';
                $pozycja->ilosc = 0;
                break;

            case 'przelozyc':
                $naszykowana_czesc->ilosc_zamontowane = 0;
                $naszykowana_czesc->ilosc_rozpisane = 0;
                $naszykowana_czesc->ilosc_do_zwrotu = $naszykowana_czesc->ilosc;
                $pozycja->opis = '__';
                $pozycja->ilosc = $naszykowana_czesc->ilosc;
                break;

            default:
                $naszykowana_czesc->ilosc_rozpisane = 0;
                $naszykowana_czesc->ilosc_zamontowane = $ilosc;
                $do_zwrotu = $naszykowana_czesc->ilosc - $ilosc;
                $naszykowana_czesc->ilosc_do_zwrotu = $do_zwrotu;
                $pozycja->opis = 'założone';
                if ($do_zwrotu > 0) {
                    $pozycja->opis = '(-' . $do_zwrotu . ') ' . $pozycja->opis;
                }
                $pozycja->ilosc = $ilosc;
                break;
        }

        if ($pozycja->is_czesc_symbol and $naszykowana_czesc->opis) {
            $pozycja->opis = trim($naszykowana_czesc->opis . ' ' . $pozycja->opis);
        }

        $naszykowana_czesc->technik_updated_at = now();
        $naszykowana_czesc->save();
        $pozycja->naszykowana_czesc_key = $naszykowana_czesc->key;
        $pozycja->save();

        return response()->json('saved', 200);
    }

    public function indexOdbior(int $technik_id = null)
    {
        $technicy = Technik::getLast();
        $technik = Technik::find($technik_id);

        $naszykowane_czesci = [];
        if ($technik) {
            $naszykowane_czesci = Naszykowana::with('kosztorys_pozycje', 'zlecenie.klient', 'zlecenie.urzadzenie', 'towar', 'user')
                ->where('technik_id', $technik->id)
                ->where(function ($query) {
                    $query->where('ilosc_do_zwrotu', '>', 0);
                    $query->orWhereNull('sprawdzone_at');
                })
                ->orderBy('zlecenie_data')->get();
        }

        return view('czesci.odbior', compact('technik', 'technicy', 'naszykowane_czesci'));
    }

    public function updateSprawdz(Request $request, Naszykowana $naszykowana_czesc)
    {
        $user = $request->user();

        $naszykowana_czesc->sprawdzil_user_id = $user->id;
        $naszykowana_czesc->sprawdzone_at = now();
        $naszykowana_czesc->ilosc_do_zwrotu = 0;
        $naszykowana_czesc->save();

        return response()->json('saved', 200);
    }

    public function apiProps(Request $request, string $prop)
    {
        switch ($prop) {
            case 'symbol':
                $prop_name = 'tw_Symbol';
                $search = "{$request->search}%";
                break;
            case 'symbol_dostawcy':
                $prop_name = 'tw_DostSymbol';
                $search = "%{$request->search}%";
                break;
        }

        $props = Subiekt_Towar::where($prop_name, 'like', $search)->orderBy($prop_name)->select($prop_name)
            ->distinct()->limit(10)->get()
            ->pluck($prop)->all();

        return response()->json($props);
    }

    public function bezZdjec()
    {
        $towary = Subiekt_Towar::where('tw_Rodzaj', 1)->without('zdjecia')->whereDoesntHave('zdjecia')->whereHas('stan', function ($query) {
            $query->where('st_Stan', '>', 0);
        })->orderBy('tw_PKWiU')->get();

        return view('czesci.bez-zdjec', compact('towary'));
    }
}
