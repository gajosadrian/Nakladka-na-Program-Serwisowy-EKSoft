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
        if ($polka and $pojemnik) {
            $_polka = preg_replace('/\s+/', '', strtolower($polka . '-' . $pojemnik));
        }
        $_towar_polka = $towar ? preg_replace('/\s+/', '', strtolower($towar->polka)) : '';

        $valid_polka = str_contains($_towar_polka, $_polka) and ($_towar_polka and $_polka);

        $stan = Stan::where('user_id', $user->id)->where('symbol', $towar->symbol)->where('polka', $_polka)->first();
        $stan_logs = StanLog::where('symbol', $towar->symbol)->get();

        return view('inwentaryzacja.show', compact('symbol', 'polka', 'pojemnik', 'towar', 'valid_polka', 'stan', 'stan_logs', '_polka'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $status = null;

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
        $stan_log->stan = $request->stan;
        $stan_log->save();

        return redirect()->back();
    }
}
