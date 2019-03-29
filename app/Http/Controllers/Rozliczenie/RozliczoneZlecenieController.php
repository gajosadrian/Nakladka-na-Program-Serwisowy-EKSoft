<?php

namespace App\Http\Controllers\Rozliczenie;

use App\Http\Controllers\Controller;
use App\Models\Rozliczenie\RozliczoneZlecenie;
use App\Models\Rozliczenie\Rozliczenie;
use App\Models\Zlecenie\Zlecenie;
use Illuminate\Http\Request;

class RozliczoneZlecenieController extends Controller
{
    /**
     * API
     *
     */
    public function apiStoreMany(Request $request)
    {
        if (empty($request->zlecenia_ids)) {
            return response()->json('success', 200);
        }

        $user = auth()->user();

        if ($request->remove) {
            foreach ($request->zlecenia_ids as $zlecenie_id) {
                $zlecenie = Zlecenie::findOrFail($zlecenie_id);

                $rozliczone_zlecenie = RozliczoneZlecenie::firstOrNew(['zlecenie_id' => $zlecenie->id]);
                $rozliczone_zlecenie->pracownik = $user->pracownik->login;
                $rozliczone_zlecenie->rozliczenie_id = 0;
                $rozliczone_zlecenie->zlecenie_id = $zlecenie->id;
                $rozliczone_zlecenie->zleceniodawca = $zlecenie->zleceniodawca;
                $rozliczone_zlecenie->save();
            }

            return response()->json('success', 200);
        }

        $rozliczenie = Rozliczenie::findOrFail($request->rozliczenie_id);
        $robocizny = $rozliczenie->robocizny;
        $dojazdy = $rozliczenie->dojazdy;

        foreach ($request->zlecenia_ids as $zlecenie_id) {
            $zlecenie = Zlecenie::findOrFail($zlecenie_id);

            $rozliczone_zlecenie = RozliczoneZlecenie::firstOrNew(['zlecenie_id' => $zlecenie->id]);
            $rozliczone_zlecenie->pracownik = $user->pracownik->login;
            $rozliczone_zlecenie->rozliczenie_id = $rozliczenie->id;
            $rozliczone_zlecenie->zlecenie_id = $zlecenie->id;
            $rozliczone_zlecenie->zleceniodawca = $zlecenie->zleceniodawca;
            $rozliczone_zlecenie->robocizny = $zlecenie->robocizny;
            $rozliczone_zlecenie->dojazdy = $zlecenie->dojazdy;
            $rozliczone_zlecenie->save();

            if ($rozliczone_zlecenie->wasRecentlyCreated) {
                $robocizny = array_sum_identical_keys($robocizny, $zlecenie->robocizny);
                $dojazdy = array_sum_identical_keys($dojazdy, $zlecenie->dojazdy);
            }
        }

        $rozliczenie->robocizny = $robocizny;
        $rozliczenie->dojazdy = $dojazdy;
        $rozliczenie->save();

        return response()->json('success', 200);
    }

   public function apiDestory(Request $request)
   {
       if (! $request->id) {
           return response()->json('error', 500);
       }

       $rozliczone_zlecenie = RozliczoneZlecenie::findOrFail($request->id);
       $rozliczenie = $rozliczone_zlecenie->rozliczenie;
       $zlecenie = $rozliczone_zlecenie->zlecenie;

       $rozliczenie->robocizny = array_sub_identical_keys($rozliczenie->robocizny, $zlecenie->robocizny);
       $rozliczenie->dojazdy = array_sub_identical_keys($rozliczenie->dojazdy, $zlecenie->dojazdy);
       $rozliczenie->save();

       $rozliczone_zlecenie->delete();

       return response()->json('success', 200);
   }
}
