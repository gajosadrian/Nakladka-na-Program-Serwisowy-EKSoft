<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inwentaryzacja\Stan;
use App\Models\Inwentaryzacja\StanLog;
use App\Models\Subiekt\Subiekt_Towar;

class InwentaryzacjaController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        $symbol = $request->symbol;
        if ($symbol) {
            $symbol = str_pad($symbol, 5, '0', STR_PAD_LEFT);
        }
        $polka = $request->polka;
        $pojemnik = $request->pojemnik;

        $towar = Subiekt_Towar::where('tw_Symbol', $symbol)->first();

        $_polka = null;
        if ($polka) { // and ($pojemnik or str_contains($polka, ['m', 'M']))
            if (!$pojemnik) { // str_contains($polka, ['m', 'M'])
                $_polka = preg_replace('/\s+/', '', strtolower($polka));
            } else {
                $_polka = preg_replace('/\s+/', '', strtolower($polka . '-' . $pojemnik));
            }
        }
        $_towar_polka = $towar ? preg_replace('/\s+/', '', strtolower($towar->polka)) : '';
        $valid_polka = str_contains($_towar_polka, $_polka) and ($_towar_polka and $_polka);

        // if ($user->id == 1) {
        //     dd($_polka);
        // }

        $stan = @Stan::where('user_id', $user->id)->where('symbol', $towar->symbol)->where('polka', $_polka)->first() ?? null;
        $stany = @Stan::where('symbol', $towar->symbol)->get() ?? null;
        $is_stany = (count($stany) > 0);
        $stan_logs = @StanLog::where('symbol', $towar->symbol)->get() ?? null;
        $is_stan_logs = (count($stan_logs) > 0);

        return view('inwentaryzacja.show', compact('symbol', 'polka', 'pojemnik', 'towar', 'valid_polka', 'stan', 'stany', 'stan_logs', '_polka', 'is_stan_logs', 'is_stany'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $status = null;

        if ($request->polka_new) {
            $towar = Subiekt_Towar::where('tw_Symbol', $request->symbol)->first();
            $towar->polka = $request->polka_new;
            $towar->save();
        }

        $stan = Stan::firstOrCreate([
            'user_id' => $user->id,
            'symbol' => $request->symbol,
            'polka' => $request->polka,
        ]);

        if ($request->stan) {
            $stan->towar_id = $request->towar_id;
            $stan->stan = $request->stan;
            $stan->save();
            if ($stan->wasRecentlyCreated) {
                $status = 'new';
            } else {
                $status = 'update';
            }
        } else {
            $stan->delete();
            $status = 'delete';
        }

        $stan_log = new StanLog;
        $stan_log->user_id = $user->id;
        $stan_log->towar_id = $request->towar_id;
        $stan_log->symbol = $request->symbol;
        $stan_log->polka = $request->polka;
        $stan_log->status = $status;
        $stan_log->stan = $request->stan ?? 0;
        $stan_log->save();

        return redirect()->back();
    }

    public function showNotChecked()
    {
        // $towary = Subiekt_Towar::with('stan')->whereHas('stan', function ($stan) {
        //     $stan->where('st_Stan', '>', 0);
        // })->whereDoesntHave('inwentaryzacja_stany', function ($inwentaryzacja_stan) {
        //     $inwentaryzacja_stan->where('stan', '>', 0);
        // })->get();

        $stany = Stan::get(['towar_id', 'symbol', 'stan']);
        $towary = Subiekt_Towar::without('zdjecia')->with('stan')->whereHas('stan', function ($stan) {
            $stan->where('st_Stan', '>', 0);
        })->get(['tw_Id', 'tw_Nazwa', 'tw_Symbol', 'tw_PKWiU'])->filter(function ($towar) use ($stany) {
            return ! (bool) $stany->where('towar_id', $towar->id)->first();
        })->sortBy('polka');

        return view('inwentaryzacja.not-checked', compact('towary'));
    }
}
