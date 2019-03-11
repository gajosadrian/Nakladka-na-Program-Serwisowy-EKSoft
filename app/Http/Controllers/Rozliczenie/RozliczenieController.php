<?php

namespace App\Http\Controllers\Rozliczenie;

use App\Models\Zlecenie;
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
        // for ($i = 42239; $i < 45182; $i++) {
        //     $rozliczone_zlecenie = new RozliczoneZlecenie;
        //     $rozliczone_zlecenie->pracownik = '';
        //     $rozliczone_zlecenie->rozliczenie_id = 0;
        //     $rozliczone_zlecenie->zlecenie_id = $i;
        //     $rozliczone_zlecenie->producent_type = '';
        //     $rozliczone_zlecenie->save();
        // }
        // return response()->json('success', 200);

        $now = now();
        $end_of_month = now()->endOfMonth();
        $diff = $now->diffInDays($end_of_month);
        $creatable_date = now()->startOfMonth();
        if ($diff >= 15) {
            $creatable_date->subMonth();
        }

        $rozliczenia = Rozliczenie::limit(12)->get();
        if ($ostatnie_rozliczenie = Rozliczenie::getLast()) {
            $is_creatable = (Carbon::create($ostatnie_rozliczenie->rok, $ostatnie_rozliczenie->miesiac)->format('Y-m') != $creatable_date->format('Y-m'));
        } else {
            $is_creatable = true;
        }

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
        $rozliczenie = Rozliczenie::with('rozliczone_zlecenia.zlecenie')->findOrFail($id);
        $rozliczone_zlecenia = $rozliczenie->rozliczone_zlecenia->sortBy('zleceniodawca');

        if (! $rozliczenie->is_closed) {
            $zlecenia_nierozliczone = Zlecenie\Zlecenie::with('status', 'terminarz', 'kosztorys_pozycje', 'rozliczenie')->latest('id_zlecenia')->limit(6000)->get();
            $zlecenia_nierozliczone = $zlecenia_nierozliczone->filter(function ($zlecenie) {
                // return !$zlecenie->is_rozliczone and $zlecenie->data_zakonczenia <= Carbon::create(2019, 1, 31)->endOfDay() and $zlecenie->status->id == 26;
                return !$zlecenie->is_rozliczone and in_array($zlecenie->status->id, [Zlecenie\Status::ZAKONCZONE_ID, Zlecenie\Status::DO_ROZLICZENIA_ID]);
            })->sortBy('data_zakonczenia');
        }

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
            'rozliczone_zlecenia_amount'
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
}
