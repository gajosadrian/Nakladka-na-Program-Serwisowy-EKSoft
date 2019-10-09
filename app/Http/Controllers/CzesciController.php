<?php

namespace App\Http\Controllers;

use App\Models\Zlecenie;
use App\Models\SMS\Technik;
use App\Models\Subiekt\Subiekt_Towar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CzesciController extends Controller
{
    public function indexMenu()
    {
        return view('czesci.menu');
    }

    public function indexSzykowanie(int $technik_id = null, string $date_string = null)
    {
        if (! $date_string) {
            $today = today();
            $date = $today->copy();
            if ($today->copy()->addHours(10)->lt(now())) {
                $date->addDay();
            }
            $date_string = $date->toDateString();
        } else {
            $date = Carbon::parse($date_string)->startOfDay();
        }

        $is_today = $date->isToday();

        $technicy = Technik::getLast();
        $technik = Technik::find($technik_id);

        $terminy = [];
        if ($technik) {
            $terminy = Zlecenie\Terminarz::getTerminy($technik->id, $date_string, [
                'do_wyjasnienia' => false,
                'has_zlecenie' => true,
            ]);
        }

        return view('czesci.szykowanie', compact('date', 'date_string', 'technik', 'technicy', 'terminy'));
    }
}
