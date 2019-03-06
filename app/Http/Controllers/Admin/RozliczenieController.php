<?php

namespace App\Http\Controllers\Admin;

use App\Models\Zlecenie;
use App\Models\Rozliczenie\Rozliczenie;
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
        $now = now();
        $end_of_month = now()->endOfMonth();
        $diff = $now->diffInDays($end_of_month);
        $creatable_date = now()->startOfMonth();
        if ($diff >= 15) {
            $creatable_date->subMonth();
        }

        $rozliczenia = Rozliczenie::all();
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
    public function show($id)
    {
        $zlecenia_nierozliczone = Zlecenie\Zlecenie::where('id_zlecenia', '>', 45000)->withRelations()->oldest('DataPrzyjecia')->get();

        return view('rozliczenia.pokaz', compact(
            'zlecenia_nierozliczone'
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

        return redirect()->route('admin.rozliczenia.lista');
    }
}
