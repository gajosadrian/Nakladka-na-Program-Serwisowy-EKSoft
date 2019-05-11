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

        $rozliczenia = Rozliczenie::limit(12)->with('rozliczone_zlecenia')->orderByDesc('id')->get();

        $now = now();
        $end_of_month = now()->endOfMonth();
        $diff = $now->diffInDays($end_of_month);
        $creatable_date = now()->startOfMonth();
        if ($diff >= 15) {
            $creatable_date->subMonth();
        }

        $ostatnie_rozliczenie = Rozliczenie::getLast();
        $is_creatable = ($ostatnie_rozliczenie->okres != $creatable_date->format('Y-m'));

        return view('rozliczenia.lista', compact(
            'is_creatable',
            'creatable_date',
            'rozliczenia'
        ));
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(int $id)
    {
        $rozliczenie = Rozliczenie::with('rozliczone_zlecenia.zlecenie.terminarz')->findOrFail($id);
        $rozliczone_zlecenia = $rozliczenie->rozliczone_zlecenia->sortBy('zleceniodawca');
        $zleceniodawcy = $rozliczenie->zleceniodawcy;

        $zlecenia_nierozliczone = null;
        if (! $rozliczenie->is_closed) {
            $zlecenia_nierozliczone = Zlecenie::getDoRozliczenia();
            $zleceniodawcy = $zleceniodawcy->merge($zlecenia_nierozliczone->unique('zleceniodawca')->pluck('zleceniodawca')->values())->unique()->sort()->values();
        }

        $zleceniodawcy->filter(function ($value) {
            return $value != Zlecenie::ODPLATNE_NAME;
        });

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
            'zleceniodawcy'
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
        $rozliczenie = Rozliczenie::with('rozliczone_zlecenia', 'rozliczone_zlecenia.zlecenie')->findOrFail($id);
        $robocizny = [];
        $dojazdy = [];

        foreach ($rozliczenie->rozliczone_zlecenia as $rozliczone_zlecenie) {
            $robocizny = array_sum_identical_keys($robocizny, $rozliczone_zlecenie->zlecenie->robocizny);
            $dojazdy = array_sum_identical_keys($dojazdy, $rozliczone_zlecenie->zlecenie->dojazdy);
        }

        $rozliczenie->robocizny = $robocizny;
        $rozliczenie->dojazdy = $dojazdy;
        $rozliczenie->save();

        return back();
    }
}
