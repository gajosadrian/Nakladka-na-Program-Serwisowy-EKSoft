<?php

namespace App\Http\Controllers\Rozliczenie;

use App\Models\Zlecenie\Zlecenie;
use App\Models\Rozliczenie\Rozliczenie;
use App\Models\Rozliczenie\RozliczoneZlecenie; // TODO remove
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RozliczenieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        // $zlecenie = Zlecenie\Zlecenie::where('NrZlecenia', 'ZS//12/18')->firstOrFail();
        // $rozliczone_zlecenie = RozliczoneZlecenie::where('zlecenie_id', $zlecenie->id)->firstOrFail();
        // $rozliczone_zlecenie->delete();

        // for ($i = 42239; $i < 45182; $i++) {
        //     $rozliczone_zlecenie = new RozliczoneZlecenie;
        //     $rozliczone_zlecenie->pracownik = '';
        //     $rozliczone_zlecenie->rozliczenie_id = 0;
        //     $rozliczone_zlecenie->zlecenie_id = $i;
        //     $rozliczone_zlecenie->producent_type = '';
        //     $rozliczone_zlecenie->save();
        // }
        // return response()->json('success', 200);

        $rozliczenia = Rozliczenie::limit(12)->with('rozliczone_zlecenia', '_pracownik')->orderByDesc('id')->get();
        $months = getMonths();

        $now = now();
        $end_of_month = now()->endOfMonth();
        $diff = $now->diffInDays($end_of_month);
        $creatable_date = now()->startOfMonth();
        if ($diff >= 30) {
            $creatable_date->subMonth();
        }

        $ostatnie_rozliczenie = Rozliczenie::getLast();
        $is_creatable = ($ostatnie_rozliczenie->okres != $creatable_date->format('Y-m'));

        return view('rozliczenia.lista', compact(
            'is_creatable',
            'creatable_date',
            'rozliczenia',
            'months',
            'now'
        ));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(int $id)
    {
        $rozliczenie = Rozliczenie::with('rozliczone_zlecenia.zlecenie.terminarz', 'rozliczone_zlecenia.zlecenie.technik', 'rozliczone_zlecenia.zlecenie.klient')->findOrFail($id);
        $rozliczone_zlecenia = $rozliczenie->rozliczone_zlecenia->sortBy('zleceniodawca');
        $zleceniodawcy = $rozliczenie->zleceniodawcy;
        $zleceniodawcy_nierozliczeni = [];

        $zlecenia_nierozliczone = null;
        if (! $rozliczenie->is_closed) {
            $zlecenia_nierozliczone = Zlecenie::getDoRozliczenia();

            $zleceniodawcy_nierozliczeni = $zlecenia_nierozliczone->filter(function ($zlecenie) use ($rozliczenie) {
                return $zlecenie->is_data_zakonczenia and $zlecenie->data->lte($rozliczenie->data) or (!$zlecenie->is_data_zakonczenia and $zlecenie->data_przyjecia->lte($rozliczenie->data));
            })->unique('zleceniodawca')->pluck('zleceniodawca')->values()->toArray();

            $zleceniodawcy = $zleceniodawcy->merge($zlecenia_nierozliczone->unique('zleceniodawca')->pluck('zleceniodawca')->values())->unique()->sort()->values();
        }

        // $zleceniodawcy = $zleceniodawcy->filter(function ($value) {
        //     return $value != Zlecenie::ODPLATNE_NAME;
        // });

        $rozliczone_zlecenia_amount = count($rozliczone_zlecenia);
        $zlecenia_nierozliczone_amount = @count($zlecenia_nierozliczone) ?? 0;

        // foreach ($zlecenia_nierozliczone as $zlecenie) {
        //     $rozliczone_zlecenie = new RozliczoneZlecenie;
        //     $rozliczone_zlecenie->pracownik = '';
        //     $rozliczone_zlecenie->rozliczenie_id = 0;
        //     $rozliczone_zlecenie->zlecenie_id = $zlecenie->id;
        //     $rozliczone_zlecenie->producent_type = '';
        //     $rozliczone_zlecenie->save();
        // }

        return view('rozliczenia.pokaz', compact(
            'rozliczenie',
            'zlecenia_nierozliczone',
            'rozliczone_zlecenia',
            'zlecenia_nierozliczone_amount',
            'rozliczone_zlecenia_amount',
            'zleceniodawcy',
            'zleceniodawcy_nierozliczeni'
        ));
    }

    public function analiza(int $id, string $zleceniodawca = null)
    {
        $rozliczenie = Rozliczenie::findOrFail($id);
        // $rozliczone_zlecenia = $rozliczenie->rozliczone_zlecenia->sortBy('zleceniodawca');
        $zleceniodawcy = $rozliczenie->zleceniodawcy;

        $is_zleceniodawca = isset($zleceniodawca);
        if ($is_zleceniodawca) {
            $zlecenia = $rozliczenie->rozliczone_zlecenia()->with('zlecenie', 'zlecenie.kosztorys_pozycje')->where('zleceniodawca', $zleceniodawca)->get();
        }

        return view('rozliczenia.analiza', compact(
            'rozliczenie',
            'zleceniodawcy',
            'is_zleceniodawca',
            'zleceniodawca',
            'zlecenia'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $rozliczenie = new Rozliczenie;
        $rozliczenie->pracownik = $user->pracownik->login;
        $rozliczenie->rok = $request->rok;
        $rozliczenie->miesiac = $request->miesiac;
        $rozliczenie->save();

        return redirect()->back();
    }

    public function hardReload(int $id)
    {
        $rozliczenie = Rozliczenie::with('rozliczone_zlecenia', 'rozliczone_zlecenia.zlecenie.kosztorys_pozycje.towar', 'rozliczone_zlecenia.zlecenie.urzadzenie')->findOrFail($id);
        $robocizny = [];
        $dojazdy = [];

        foreach ($rozliczenie->rozliczone_zlecenia ?? [] as $rozliczone_zlecenie) {
            $zlecenie = $rozliczone_zlecenie->zlecenie;
            $robocizny = array_sum_identical_keys($robocizny, $zlecenie->robocizny);
            $dojazdy = array_sum_identical_keys($dojazdy, $zlecenie->dojazdy);

            $rozliczone_zlecenie->zleceniodawca = $zlecenie->zleceniodawca;
            $rozliczone_zlecenie->robocizny = $zlecenie->robocizny;
            $rozliczone_zlecenie->dojazdy = $zlecenie->dojazdy;
            $rozliczone_zlecenie->save();
        }

        $rozliczenie->robocizny = $robocizny;
        $rozliczenie->dojazdy = $dojazdy;
        $rozliczenie->save();

        return redirect()->back();
    }
}
