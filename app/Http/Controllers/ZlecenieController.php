<?php

namespace App\Http\Controllers;

use Facades\App\Zlecenie;
use App\Models;
use Illuminate\Http\Request;
use Carbon\Carbon; // TODO remove

class ZlecenieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $zleceniaNiezakonczone = Zlecenie::getNiezakonczone([
            'technik_id' => $user->technik_id,
        ]);

        return view('zlecenie.lista', [
            'zlecenia' => $zleceniaNiezakonczone,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('zlecenie.show', [
            'zlecenie' => Zlecenie::find($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
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

        $zlecenie->opis .= "\r\n** " . $user->short_name . " dnia " . date('d.m H:i') . ": „" . $request->opis . "”";
        $zlecenie->save();

        return response()->json($zlecenie->opis, 200);
    }

    public function apiChangeStatus(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $status_historia = new Models\Zlecenie\StatusHistoria;
        $status_historia->zlecenie_id = $zlecenie->id;
        $status_historia->pracownik_id = $user->pracownik->id;
        $status_historia->data = Carbon::now()->format('Y-m-d H:i:s.000');
        $status_historia->nr_o_zlecenia = null;
        $status_historia->save();

        return response()->json($status_historia, 200);
    }
}
