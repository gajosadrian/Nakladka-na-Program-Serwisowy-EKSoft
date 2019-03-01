<?php

namespace App\Http\Controllers;

use Facades\App\Zlecenie;
use Illuminate\Http\Request;

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

        $zlecenie->appendOpis($request->opis, $user->short_name);

        return response()->json($zlecenie->opis, 200);
    }

    public function apiChangeStatus(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->changeStatus($request->status_id, $user->pracownik->id, $request->remove_termin ?? false);

        return response()->json('success', 200);
    }
}
