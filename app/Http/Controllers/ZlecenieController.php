<?php

namespace App\Http\Controllers;

use Facades\App\Models\Zlecenie\Zlecenie;
use Facades\App\Models\Zlecenie\Terminarz;
use Facades\App\Models\SMS\Technik;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ZlecenieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = auth()->user();
        $zlecenia_niezakonczone = Zlecenie::getNiezakonczone([
            'technik_id' => $user->technik_id,
        ]);

        return view('zlecenie.lista', [
            'zlecenia' => $zlecenia_niezakonczone,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        $zlecenie = Zlecenie::find($id);

        return view('zlecenie.pokaz', compact(
            'zlecenie'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Request $request)
    {
        //
    }

    public function dlaTechnika(int $technik_id = null, int $timestamp = null)
    {
        $user = auth()->user();

        $can_choose_technik = (bool) ($user->technik_id == 0);

        $technicy = Technik::getLast();
        $technik_id = ($user->technik_id > 0) ? $user->technik_id : $technik_id;
        $technik = Technik::find($technik_id);

        $now = now();
        if ($timestamp) {
            $date = Carbon::createFromTimestamp($timestamp);
        } elseif ($now->copy()->startOfDay()->addHours(10)->lt($now)) {
            $date = $now->copy()->addDay();
        } else {
            $date = $now;
        }
        $date_string = $date->toDateString();
        $date_formatted = $date->format('d-m-Y');

        // $zlecenia = null;
        $terminy = null;
        $terminarz_notatki = null;
        $kierowca = null;
        if ($technik) {
            // $zlecenia = Zlecenie::withRelations()->whereHas('terminarz', function ($query) use ($date_string, $technik) {
            //     $query->where('STARTDATE', '>=', $date_string . ' 00:00:01');
            //     $query->where('ENDDATE', '<=', $date_string . ' 23:59:59');
            //     $query->where('id_techn_term', $technik->id);
            // })->get()->sortBy('terminarz.data_rozpoczecia');
            $terminy = Terminarz::with('zlecenie.klient', 'zlecenie.urzadzenie', 'zlecenie.kosztorys_pozycje', 'zlecenie.status')
                ->where('STARTDATE', '>=', $date_string . ' 00:00:01')
                ->where('ENDDATE', '<=', $date_string . ' 23:59:59')
                ->where('id_techn_term', $technik->id)
                ->orderBy('STARTDATE')
                ->get();

            $terminarz_notatki = Terminarz::where('STARTDATE', $date_string . ' 00:00:00:000')
                ->where('id_techn_term', $technik->id)
                ->get()->filter(function ($v) {
                    return ! $v->is_samochod;
                });

            $samochod = Terminarz::getSamochod($technik->id, $date_string);
        }

        return view('zlecenie.dla-technika', compact(
            'can_choose_technik',
            'technicy',
            'technik_id',
            'technik',
            'timestamp',
            'now',
            'date',
            'date_string',
            'date_formatted',
            'terminy',
            'terminarz_notatki',
            'samochod'
        ));
    }

    public function apiGetOpis(Request $request, int $id)
    {
        $zlecenie = Zlecenie::find($id);

        return response()->json($zlecenie->opis, 200);
    }

    public function apiAppendNotatka(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->appendOpis($request->opis, $user->short_name);
        $zlecenie->save();

        return response()->json($zlecenie->opis, 200);
    }

    public function apiChangeStatus(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->changeStatus($request->status_id, $user->pracownik->id, $request->remove_termin ?? false);
        $zlecenie->save();

        return response()->json('success', 200);
    }
}
