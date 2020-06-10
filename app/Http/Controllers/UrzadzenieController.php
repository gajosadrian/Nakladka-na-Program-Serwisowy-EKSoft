<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zlecenie\Urzadzenie;
use App\Models\Zlecenie\Zlecenie;

class UrzadzenieController extends Controller
{
    public function zdjecia(Request $request)
    {
        if ($request->wantsJson()) {
            $urzadzenia_ids = [];
            $date_start = $request->date_start;
            $date_end = $request->date_end;

            if (! $date_start or ! $date_end) {
                return response()->json(['zlecenia' => []]);
            }

            $zlecenia = collect();
            $_zlecenia = Zlecenie::whereHas('urzadzenie')->whereHas('terminarz', function ($q) use ($date_start, $date_end) {
                $q->where('STARTDATE', '>=', $date_start . ' 00:00:01');
                $q->where('ENDDATE', '<=', $date_end . ' 23:59:59');
            })->with('urzadzenie', 'zdjecia_do_urzadzenia', 'technik')->latest('id_zlecenia')->get();
            $_zlecenia->each(function ($zlecenie) use (&$urzadzenia_ids, $zlecenia) {
                if (! in_array($zlecenie->urzadzenie->id, $urzadzenia_ids)) {
                    $urzadzenia_ids[] = $zlecenie->urzadzenie->id;

                    $zlecenie->urzadzenie = $zlecenie->urzadzenie->only('id', 'producent', 'nazwa', 'model', 'kod_wyrobu', 'nr_seryjny', 'nr_seryjny_raw');
                    $zlecenie->zdjecia_do_urzadzenia = $zlecenie->zdjecia_do_urzadzenia->map->only('id', 'type', 'path', 'url');

                    $zlecenia->push($zlecenie);
                }
            });
            $zlecenia = $zlecenia->map->only('id', 'urzadzenie', 'zdjecia_do_urzadzenia', 'technik', 'popup_link', 'nr');

            return response()->json(compact('zlecenia'));
        }

        return view('urzadzenie.zdjecia');
    }

    public function apiProps(Request $request, string $prop)
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
            case 'kod_wyrobu':
                $prop_name = 'asset';
                break;
        }

        $props = Urzadzenie::where($prop_name, 'like', "{$request->search}%")->orderBy($prop_name)->select($prop_name)
            ->distinct()->limit(10)->get()
            ->pluck($prop)->all();

        return response()->json($props);
    }

    public function apiSerialNo(Request $request)
    {
        $urzadzenie = Urzadzenie::where('SERIAL_NO', $request->search)->select('SERIAL_NO')->first();

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
