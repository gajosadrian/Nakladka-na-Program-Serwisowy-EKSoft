<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Zdjecie;
use Illuminate\Support\Facades\DB;

class ZlecenieZdjecieController extends Controller
{
    public function show(Request $request, int $zlecenie_id)
    {
        $zlecenie = Zlecenie::with(['zdjecia_do_zlecenia', 'zdjecia_do_urzadzenia'])->findOrFail($zlecenie_id);

        if ( $request->wantsJson() ) {
            return response()->json([
                'zlecenie' => $zlecenie->only('id', 'is_gwarancja', 'is_ubezpieczenie', 'is_odplatne'),
                'urzadzenie' => $zlecenie->urzadzenie_id ? $zlecenie->urzadzenie->only('id') : null,
                'zdjecia' => $zlecenie->zdjecia->map->only('id', 'zlecenie_id', 'urzadzenie_id', 'type', 'url'),
                'no_img_url' => asset('media/no-img.jpg'),
            ]);
        }

        return view('zlecenie.zdjecia', compact('zlecenie'));
    }

    public function show2(int $zlecenie_id)
    {
        // $zlecenie = Zlecenie::with(['zdjecia_do_zlecenia', 'zdjecia_do_urzadzenia'])->findOrFail($zlecenie_id);
        $zlecenie = Zlecenie::findOrFail($zlecenie_id);

        return view('zlecenie.zdjecia2', compact('zlecenie'));
    }

    public function make(Zdjecie $zdjecie)
    {
        return response()->file(
            Storage::disk('images')->path($zdjecie->path)
        );
    }

    public function store(Request $request)
    {
        if ((! $request->zlecenie_id and ! $request->urzadzenie_id) or ! $request->image or ! $request->type) abort(400);
        // abort(400);

        DB::transaction(function () use ($request) {
            $path2 = null;

            if ($technik = $request->user()->technik) {
                $zlecenie = Zlecenie::find($request->zlecenie_id);
                if ($zlecenie->terminarz) {
                    $date = $zlecenie->data;
                } else {
                    $date = today();
                }
                $months = getMonths();
                $month_format = $date->format('m');
                $month_name = $months->where('id', $date->month)->first()->name;
                $image_original_name = $request->image->getClientOriginalName();

                $path2 = Storage::disk('zdjecia_technikow')->putFileAs(sprintf(Zdjecie::TECHNIK_PATH, 2020, ($month_format.' '.$month_name), $technik->imie, $date->format('d.m')), $request->image, $image_original_name);
            }

            Zdjecie::create([
                'zlecenie_id' => ($request->save_to == 'zlecenie') ? $request->zlecenie_id : 0,
                'urzadzenie_id' => ($request->save_to == 'urzadzenie') ? $request->urzadzenie_id : 0,
                'type' => $request->type,
                'path' => Storage::disk('images')->putFile('', $request->image),
                'path2' => $path2,
            ]);
        });

        return response()->json('success', 200);
    }

    public function destroy(Zdjecie $zdjecie)
    {
        if (! $zdjecie->is_deletable) abort(401);

        Storage::disk('images')->delete('', $zdjecie->path);
        if ($zdjecie->path2) Storage::disk('zdjecia_technikow')->delete('', $zdjecie->path2);

        $zdjecie->delete();

        return redirect()->back();
    }
}
