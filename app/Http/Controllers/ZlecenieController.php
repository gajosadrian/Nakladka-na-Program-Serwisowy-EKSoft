<?php

namespace App\Http\Controllers;

use App\Zlecenie;
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
        $zleceniaNiezakonczone = Zlecenie::getNiezakonczone();

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
    public function show(Zlecenie $zlecenie)
    {
        return view('zlecenie.show', [
            'zlecenie' => $zlecenie,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function edit(Zlecenie $zlecenie)
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
    public function update(Request $request, Zlecenie $zlecenie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Zlecenie  $zlecenie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zlecenie $zlecenie)
    {
        //
    }
}
