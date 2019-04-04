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

        $technicy = Technik::getLast();
        $technik_id = ($user->technik_id > 0) ? $user->technik_id : $technik_id;
        $technik = Technik::find($technik_id);

        $date = $timestamp ? Carbon::createFromTimestamp($timestamp) : now();
        // $date_string = $date->toDateString();
        $date_string = '2018-08-06';
        $date_formatted = $date->format('d-m-Y');

        $zlecenia = Zlecenie::withRelations()->whereHas('terminarz', function ($query) use ($date_string) {
            $query->where('STARTDATE', '>=', $date_string);
            $query->where('ENDDATE', '<=', $date_string . ' 23:59:59');
        })->get();

        return view('zlecenie.dla-technika', compact(
            'technicy',
            'technik_id',
            'technik',
            'timestamp',
            'date',
            'date_formatted',
            'zlecenia'
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
