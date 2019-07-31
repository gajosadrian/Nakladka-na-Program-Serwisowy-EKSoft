<?php

namespace App\Http\Controllers;

use Facades\App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Terminarz;
use App\Models\Zlecenie\Status;
use App\Models\Zlecenie\KosztorysPozycja;
use App\Models\Zlecenie\ZatwierdzonyBlad;
use App\Models\SMS\Technik;
use App\Models\Subiekt\Subiekt_Towar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ZlecenieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $user = auth()->user();
        $zlecenia_niezakonczone = Zlecenie::getNiezakonczone([
            'technik_id' => $user->technik_id,
        ]);
        $search_value = $user->getSavedField('zlecenia.search');
        $autorefresh = (bool) $user->technik_id;

        return view('zlecenie.lista', [
            'zlecenia' => $zlecenia_niezakonczone,
            'search_value' => $search_value,
            'autorefresh' => $autorefresh,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        $zlecenie = Zlecenie::findOrFail($id);

        return view('zlecenie.pokaz', compact(
            'zlecenie'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Request $request)
    {
        //
    }

    public function dlaTechnika(int $technik_id = null, int $timestamp = null)
    {
        $user = auth()->user();
        $is_technik = (bool) ($user->technik_id != 0);

        $technicy = Technik::getLast();
        $technik_id = ($user->technik_id > 0) ? $user->technik_id : $technik_id;
        $technik = Technik::find($technik_id);

        $now = now();
        $now_startOfDay = $now->copy()->startOfDay();
        if ($timestamp) {
            $date = Carbon::createFromTimestamp($timestamp);
        } elseif (!$is_technik and $now_startOfDay->copy()->addHours(10)->lt($now)) {
            $date = $now->copy()->addDay();
        } else {
            $date = $now->copy();
        }
        $date_string = $date->toDateString();
        $date_formatted = $date->format('d-m-Y');
        $is_up_to_date = $now_startOfDay->lte($date->copy()->endOfDay());

        // $zlecenia = null;
        $terminy = null;
        $terminarz_notatki = null;
        $kierowca = null;
        if ($technik) {
            $zlecenia_do_wyjasnienia_symbole = Terminarz::getZleceniaDoWyjasnieniaSymbole($technik->id, $date_string);

            // $zlecenia = Zlecenie::withRelations()->whereHas('terminarz', function ($query) use ($date_string, $technik) {
            //     $query->where('STARTDATE', '>=', $date_string . ' 00:00:01');
            //     $query->where('ENDDATE', '<=', $date_string . ' 23:59:59');
            //     $query->where('id_techn_term', $technik->id);
            // })->get()->sortBy('terminarz.data_rozpoczecia');
            $terminy = Terminarz::with('zlecenie.klient', 'zlecenie.urzadzenie', 'zlecenie.kosztorys_pozycje', 'zlecenie.status_historia')
                ->where(function ($query) use ($date_string, $technik) {
                    $query->where('STARTDATE', '>=', $date_string . ' 00:00:01');
                    $query->where('ENDDATE', '<=', $date_string . ' 23:59:59');
                    $query->where('id_techn_term', $technik->id);
                })
                ->orWhereHas('zlecenie', function ($query) use ($zlecenia_do_wyjasnienia_symbole) {
                    $query->whereIn('NrZlecenia', $zlecenia_do_wyjasnienia_symbole);
                })
                ->orderBy('STARTDATE')
                ->get();

            $terminy->each(function ($termin) use ($zlecenia_do_wyjasnienia_symbole) {
                if (in_array($termin->zlecenie->nr, $zlecenia_do_wyjasnienia_symbole)) {
                    $termin->zlecenie->_do_wyjasnienia = true;
                }
            });

            $terminarz_notatki = Terminarz::where('STARTDATE', $date_string . ' 00:00:00:000')
                ->where('id_techn_term', $technik->id)
                ->get()->filter(function ($v) {
                    return !$v->is_samochod and !$v->is_zlecenie_do_wyjasnienia;
                });

            $samochod = Terminarz::getSamochod($technik->id, $date_string);
        }

        return view('zlecenie.dla-technika', compact(
            'is_technik',
            'technicy',
            'technik_id',
            'technik',
            'timestamp',
            'now',
            'date',
            'date_string',
            'date_formatted',
            'is_up_to_date',
            'terminy',
            'terminarz_notatki',
            'samochod'
        ));
    }

    public function kilometrowka(int $technik_id = null, int $month_id = null)
    {
        $user = auth()->user();
        $is_technik = (bool) ($user->technik_id != 0);
        $months = getMonths();

        $technicy = Technik::getLast();
        $technik_id = ($user->technik_id > 0) ? $user->technik_id : $technik_id;
        $technik = Technik::find($technik_id);

        $now = now();
        if (! $month_id) {
            $date_from = Carbon::create($now->year, $now->month, 1)->subMonth()->startOfDay();
            $month_id = $date_from->month;
        } else {
            $date_from = Carbon::create($now->year, $month_id, 1)->startOfDay();
        }
        $date_to = $date_from->copy()->endOfMonth()->endOfDay();

        $month = $months->where('id', $month_id)->first();

        $terminy = null;
        if ($technik) {
            $terminy = Terminarz::with('technik', 'zlecenie', 'zlecenie.klient', 'zlecenie.status_historia')
                ->where('STARTDATE', '>=', $date_from->toDateString() . ' 00:00:00')
                ->where('ENDDATE', '<=', $date_to->toDateString() . ' 23:59:59')
                ->orderBy('STARTDATE')
                ->get()
                ->filter(function ($termin) {
                    return !$termin->is_samochod and !$termin->has_dzwonic;
                })
                ->groupBy(function ($termin) {
                    return $termin->samochod['value'][1];
                });

            if (isset($terminy[$technik->id])) {
                $terminy = $terminy[$technik->id];
                $grouped_terminy = $terminy->groupBy('date_string');
            }
        }

        return view('rozliczenia.kilometrowka', compact('months', 'month', 'is_technik', 'technicy', 'technik_id', 'technik', 'month_id', 'date_from', 'date_to', 'grouped_terminy'));
    }

    public function wyszukiwanieCzesci(Request $request, string $symbol = null)
    {
        $towar = Subiekt_Towar::where('tw_Symbol', $request->symbol ?? $symbol)->first();
        $towar_id = @$towar->id ?? null;

        $kosztorys_pozycje = KosztorysPozycja::with('zlecenie', 'zlecenie.status', 'zlecenie.urzadzenie', 'zlecenie.terminarz')->where('id_o_tw', $towar_id)->orderByDesc('id')->limit(20)->get();

        return view('zlecenie.wyszukiwanie-czesci', compact('towar', 'towar_id', 'kosztorys_pozycje'));
    }

    public function apiZatwierdzBlad(Request $request, int $id)
    {
        $user = $request->user();
        $zlecenie = Zlecenie::findOrFail($id);

        $zlecenie->zatwierdzony_blad()->delete();

        $zatwierdzony_blad = new ZatwierdzonyBlad;
        $zatwierdzony_blad->status_id = $zlecenie->status_id;
        $zlecenie->zatwierdzony_blad()->save($zatwierdzony_blad);

        return response()->json('success', 200);
    }

    public function apiGetTerminarzStatusy(Request $request, int $technik_id, string $date_string = null)
    {
        $user = $request->user();

        $technik_id = ($user->technik_id > 0) ? $user->technik_id : $technik_id;
        $technik = Technik::find($technik_id);

        $date_string = $date_string ?? now()->toDateString();

        $terminy = Terminarz::with('zlecenie.status_historia')->where('STARTDATE', '>=', $date_string . ' 00:00:01')->where('ENDDATE', '<=', $date_string . ' 23:59:59')
            ->where('id_techn_term', $technik->id)
            ->orderBy('STARTDATE')
            ->get();
        $terminarz_notatki = Terminarz::where('STARTDATE', $date_string . ' 00:00:00:000')
            ->where('id_techn_term', $technik->id)
            ->get();
        $terminy = $terminy->merge($terminarz_notatki);

        $array = [];
        foreach ($terminy as $termin) {
            $array[] = [
                'id' => $termin->id,
                'status_id' => $termin->status_id,
                'last_status_nie_odbiera' => $termin->zlecenie->last_status_nie_odbiera->data_formatted ?? null,
            ];
        }

        return response()->json($array, 200);
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

        $zlecenie->appendOpis($request->opis, $user->short_name, ($user->technik_id == 0));
        $zlecenie->save();

        return response()->json($zlecenie->opis, 200);
    }

    public function apiChangeStatus(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->changeStatus($request->status_id, $user->pracownik->id, $request->remove_termin ?? false);
        $zlecenie->save();

        return response()->json('success', 200);
    }

    public function apiUmowKlienta(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->changeStatus(Status::UMOWIONO_ID, $user->pracownik->id, false);
        $zlecenie->save();
        if (! $request->dzwonic_wczesniej) {
            $zlecenie->terminarz->status_id = Terminarz::UMOWIONO_ID;
        } else {
            $zlecenie->terminarz->status_id = Terminarz::DZWONIC_WCZESNIEJ_ID;
            $zlecenie->terminarz->temat = Terminarz::DZWONIC_WCZESNIEJ_STR;
        }
        $zlecenie->terminarz->save();

        return response()->json('success', 200);
    }

    public function apiNieOdbiera(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::find($id);

        $zlecenie->changeStatus(Status::NIE_ODBIERA_ID, $user->pracownik->id, false);
        $zlecenie->changeStatus(Status::GOTOWE_DO_WYJAZDU_ID, $user->pracownik->id, false);
        $zlecenie->save();

        return response()->json('success', 200);
    }
}
