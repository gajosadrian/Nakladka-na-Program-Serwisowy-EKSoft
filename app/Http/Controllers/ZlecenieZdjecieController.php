<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Zdjecie;

class ZlecenieZdjecieController extends Controller
{
    public function show(int $zlecenie_id)
    {
        $zlecenie = Zlecenie::with(['zdjecia_do_zlecenia', 'zdjecia_do_urzadzenia'])->findOrFail($zlecenie_id);
        // return response()->json($zlecenie->zdjecia, 200);
        return view('zlecenie.zdjecia', compact('zlecenie'));
    }

    public function make(Zdjecie $zdjecie)
    {
        return response()->file(
            Storage::disk('images')->path($zdjecie->path)
        );
    }

    public function store(Request $request)
    {
        if ((!$request->zlecenie_id and !$request->urzadzenie_id) or !$request->image or !$request->type) abort(401);

        $technik = $request->user()->technik;
        if ($technik) {
            $image_original_name = $request->image->getClientOriginalName();
            $today = today();
            $months = getMonths();
            $month_format = $today->format('m');
            $month_name = $months->where('id', $today->month)->first()->name;

            Storage::disk('zdjecia_technikow')->putFileAs(sprintf(Zdjecie::TECHNIK_PATH, 2020, ($month_format.' '.$month_name), $technik->imie, $today->format('d.m')), $request->image, $image_original_name);
        }

        Zdjecie::create([
            'zlecenie_id' => ($request->save_to == 'zlecenie') ? $request->zlecenie_id : 0,
            'urzadzenie_id' => ($request->save_to == 'urzadzenie') ? $request->urzadzenie_id : 0,
            'type' => $request->type,
            'path' => Storage::disk('images')->putFile('', $request->image),
        ]);

        return response()->json('success', 200);
    }

    public function destroy(Zdjecie $zdjecie)
    {
        if ( ! $zdjecie->is_deletable) abort(401);

        Storage::disk('images')->delete('', $zdjecie->path);
        $zdjecie->delete();

        return redirect()->back();
    }
}
