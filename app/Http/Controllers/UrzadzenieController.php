<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zlecenie\Urzadzenie;
use App\Models\Zlecenie\Zlecenie;

class UrzadzenieController extends Controller
{
    public function zdjecia(Request $request)
    {
        $page = (int) $request->page ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $urzadzenia_ids = [];

        if ($request->wantsJson()) {
            $zlecenia = Zlecenie::whereHas('urzadzenie')->with('urzadzenie', 'zdjecia_do_urzadzenia')->latest('id_zlecenia')->offset($offset)->limit($limit)->get();
            $zlecenia->each(function ($zlecenie, $index) use (&$urzadzenia_ids) {
                if (! in_array($zlecenie->urzadzenie->id, $urzadzenia_ids)) {
                    $urzadzenia_ids[] = $zlecenie->urzadzenie->id;

                    $zlecenie->urzadzenie = $zlecenie->urzadzenie->only('id', 'producent', 'nazwa', 'model', 'kod_wyrobu', 'nr_seryjny', 'nr_seryjny_raw');
                    $zlecenie->zdjecia_do_urzadzenia = $zlecenie->zdjecia_do_urzadzenia->map->only('id', 'type', 'path', 'url');
                } else {
                    unset($zlecenia[$index]);
                }
            });
            $zlecenia = $zlecenia->map->only('id', 'urzadzenie', 'zdjecia_do_urzadzenia');

            return response()->json([
                'zlecenia' => $zlecenia,
            ]);
        }

        return view('urzadzenie.zdjecia');
    }

    public function apiProps(string $prop, string $search)
    {
        switch ($prop) {
            case 'producent':
                $prop_name = 'KATEGORIA';
                break;
            case 'nazwa':
                $prop_name = 'NAZWA_MASZ';
                break;
            case 'model':
                $prop_name = 'TYP_MASZ';
                break;
            case 'nr_seryjny_raw':
                $prop_name = 'SERIAL_NO';
                break;
        }

        $props = Urzadzenie::where($prop_name, 'like', "{$search}%")->orderBy($prop_name)->select($prop_name)
            ->distinct()->limit(10)->get()
            ->pluck($prop)->all();

        return response()->json($props);
    }

    public function apiSerialNo(string $search)
    {
        $urzadzenie = Urzadzenie::where('SERIAL_NO', $search)->select('SERIAL_NO')->first();

        if ($urzadzenie) {
            $found = true;
            $serial_no = $urzadzenie->nr_seryjny_raw;
        } else {
            $found = false;
            $serial_no = null;
        }

        return response()->json(compact('found', 'serial_no'));
    }

    public function putUrzadzenie(Request $request, Urzadzenie $urzadzenie)
    {
        $urzadzenie->producent = $request->producent;
        $urzadzenie->nazwa = $request->nazwa;
        $urzadzenie->model = $request->model ?? '!';
        $urzadzenie->nr_seryjny = $request->nr_seryjny_raw;
        $urzadzenie->kod_wyrobu = $request->kod_wyrobu;
        $urzadzenie->save();

        return response()->json('success');
    }
}
