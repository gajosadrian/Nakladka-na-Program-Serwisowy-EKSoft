<?php

namespace App\Http\Controllers;

use Facades\App\Models\Zlecenie\Zlecenie;
use App\Models\Zlecenie\Terminarz;
use App\Models\Zlecenie\Status;
use App\Models\Zlecenie\StatusHistoria;
use App\Models\Zlecenie\KosztorysPozycja;
use App\Models\Zlecenie\ZatwierdzonyBlad;
use App\Models\SMS\Technik;
use App\Models\Subiekt\Subiekt_Towar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ZlecenieController extends Controller
{
    public function mobileApp()
    {
        $user = auth()->user();
        if (! $user->technik_id) {
            return abort(403);
        }
        return view('zlecenie.mobile-app');
    }

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

        $zlecenia_unique = $zlecenia_niezakonczone->unique('nr_obcy');
        $zlecenia_duplicate_nr_obce = [];
        foreach ($zlecenia_niezakonczone->where('nr_obcy')->diff($zlecenia_unique)->groupBy('nr_obcy') as $zlecenie_duplicate) {
            if ($zlecenie_duplicate->count() == 1) {
                $zlecenie = $zlecenie_duplicate[0];
                $zlecenia_duplicate_nr_obce[] = $zlecenie->nr_obcy;
            }
        }
        $zlecenia_duplicate = $zlecenia_niezakonczone->whereIn('nr_obcy', $zlecenia_duplicate_nr_obce)->groupBy('nr_obcy');

        $zlecenia_ukonczone = $zlecenia_niezakonczone->filter(function ($zlecenie) {
            return in_array($zlecenie->status_id, [
                Status::ZAKONCZONE_ID,
                Status::ODWOLANO_ID,
                Status::WNIOSEK_O_WYMIANE_ID,
                Status::DO_ODBIORU_ID,
                Status::CZESCI_DO_WYSLANIA_ID,
                Status::DO_ROZLICZENIA_ID,
                Status::DZWONIC_PO_ODBIOR_ID,
            ]);
        });
        $zlecenia_ukonczone_n = $zlecenia_ukonczone->count();
        $zlecenia_n = $zlecenia_niezakonczone->count();
        $zlecenia_realizowane_n = $zlecenia_n - $zlecenia_ukonczone_n;

        return view('zlecenie.lista', [
            'zlecenia' => $zlecenia_niezakonczone,
            'zlecenia_duplicate' => $zlecenia_duplicate,
            'search_value' => $search_value,
            'autorefresh' => $autorefresh,
            'zlecenia_ukonczone_n' => $zlecenia_ukonczone_n,
            'zlecenia_realizowane_n' => $zlecenia_realizowane_n,
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
        $statusy_aktywne = Status::getAktywne();

        return view('zlecenie.pokaz', compact(
            'zlecenie',
            'statusy_aktywne'
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

        $terminy = null;
        $terminarz_notatki = null;
        $kierowca = null;
        if ($technik) {
            $terminy = Terminarz::getTerminy($technik->id, $date_string);

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
                ->where('STARTDATE', '>=', $date_from->toDateString() . ' 00:00:01')
                ->where('ENDDATE', '<=', $date_to->toDateString() . ' 23:59:59')
                ->orderBy('STARTDATE')
                ->get()
                ->filter(function ($termin) {
                    return !$termin->is_samochod; // and !$termin->has_dzwonic
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

    public function wyszukiwanieZlecenia(Request $request, string $nr_zlec = null)
    {
        $nr_zlec = $request->nr_zlec ?? $nr_zlec;
        $zlecenia = null;

        if ($nr_zlec) {
            if (str_contains($nr_zlec, ['zs', 'ZS'])) {
                $where = ['NrZlecenia', '=', $nr_zlec];
            } else {
                $where = ['NrObcy', 'like', '%'.$nr_zlec.'%'];
            }

            $zlecenia = Zlecenie::with('status', 'terminarz')->where($where[0], $where[1], $where[2])->orderByDesc('id_zlecenia')->get();
        }

        return view('zlecenie.wyszukiwanie-zlecenia', compact('zlecenia', 'nr_zlec'));
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

    public function apiRemoveStatus(Request $request, int $status_id)
    {
        $user = $request->user();
        $status = StatusHistoria::findOrFail($status_id);

        $status->delete();

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
        $zlecenie = Zlecenie::findOrFail($id);

        return response()->json($zlecenie->opis, 200);
    }

    public function apiAppendNotatka(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::findOrFail($id);

        $zlecenie->appendOpis($request->opis, $user->short_name, ($user->technik_id == 0));
        $zlecenie->save();

        return response()->json($zlecenie->opis, 200);
    }

    public function apiChangeStatus(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::findOrFail($id);

        $zlecenie->changeStatus($request->status_id, $user->pracownik->id, $request->remove_termin ?? false);
        $zlecenie->save();

        if ($terminarz_status_id = $request->terminarz_status_id) {
            $zlecenie->terminarz->status_id = $terminarz_status_id;
            $zlecenie->terminarz->save();
        }

        return response()->json('success', 200);
    }

    public function apiUmowKlienta(Request $request, int $id)
    {
        $user = auth()->user();
        $zlecenie = Zlecenie::findOrFail($id);

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
        $zlecenie = Zlecenie::findOrFail($id);

        $zlecenie->changeStatus(Status::NIE_ODBIERA_ID, $user->pracownik->id, false);
        $zlecenie->changeStatus(Status::GOTOWE_DO_WYJAZDU_ID, $user->pracownik->id, false, 1);
        $zlecenie->save();

        return response()->json('success', 200);
    }

    public function apiGetFromTerminarz(string $date_string = null)
    {
        $user = auth()->user();
        if (! $user->technik_id) {
            abort(403);
        }
        if (! $date_string) {
            $date_string = now()->toDateString();
        }

        $array = [];
        $terminy = Terminarz::getTerminy($user->technik_id, $date_string);
        foreach ($terminy as $termin) {
            $item = [
                'temat' => $termin->temat,
                'godzina_rozpoczecia' => $termin->godzina_rozpoczecia,
                'przeznaczony_czas_formatted' => $termin->przeznaczony_czas_formatted,
                'zlecenie' => null,
            ];
            if ($termin->zlecenie->klient) {
                $status_historia_preautoryzacja = $termin->zlecenie->getStatusHistoriaAt($date_string, Status::PREAUTORYZACJA_ID);
                $is_soft_zakonczone = (bool) ($termin->zlecenie->is_zakonczone or $status_historia_preautoryzacja);
                $item['zlecenie'] = [
                    'id' => $termin->zlecenie->id,
                    'nr' => $termin->zlecenie->nr,
                    'nr_obcy' => $termin->zlecenie->nr_obcy,
                    'opis' => $termin->zlecenie->opis,
                    'checkable_umowiono' => $termin->data_rozpoczecia->isToday() and !$is_soft_zakonczone,
                    'is_do_wyjasnienia' => $termin->zlecenie->_do_wyjasnienia ?? false,
                    'is_warsztat' => $termin->zlecenie->is_warsztat,
                    'is_umowiono' => $termin->is_umowiono and $termin->zlecenie->is_umowiono,
                    'is_dzwonic' => $termin->zlecenie->is_dzwonic,
                    'is_zakonczone' => $termin->zlecenie->is_zakonczone,
                    'is_soft_zakonczone' => $is_soft_zakonczone,
                    'is_preautoryzacja' => (bool) $status_historia_preautoryzacja,
                    'preautoryzacja_at' => $status_historia_preautoryzacja ? $status_historia_preautoryzacja->data->format('Y-m-d H:i') : null,
                    'znacznik_formatted' => $termin->zlecenie->znacznik_formatted,
                    'znacznik_icon' => $termin->zlecenie->znacznik->icon,
                    'czas_trwania' => $termin->zlecenie->czas_trwania,
                    'przyjmujacy_nazwa' => $termin->zlecenie->przyjmujacy->nazwa,
                    'umowiono_pracownik_nazwa' => $termin->zlecenie->last_status_umowiono->pracownik->nazwa ?? null,
                    'umowiono_data' => ($termin->zlecenie->last_status_umowiono ? $termin->zlecenie->last_status_umowiono->data->format('m.d H:i') : null),
                    'google_maps_route_link' => $termin->zlecenie->google_maps_route_link,
                    'klient' => [
                        'symbol' => $termin->zlecenie->klient->symbol,
                        'nazwa' => $termin->zlecenie->klient->nazwa,
                        'kod_pocztowy' => $termin->zlecenie->klient->kod_pocztowy,
                        'miasto' => $termin->zlecenie->klient->miasto,
                        'adres' => $termin->zlecenie->klient->adres,
                        'telefony' => $termin->zlecenie->klient->telefony_array,
                    ],
                    'kosztorys_pozycje' => $termin->zlecenie->getKosztorysArray(),
                    'urzadzenie' => null,
                ];
                if ($termin->zlecenie->urzadzenie) {
                    $item['zlecenie']['urzadzenie'] = [
                        'producent' => $termin->zlecenie->urzadzenie->producent,
                        'nazwa' => $termin->zlecenie->urzadzenie->nazwa,
                        'model' => $termin->zlecenie->urzadzenie->model ?: 'Brak modelu',
                        'nr_seryjny' => $termin->zlecenie->urzadzenie->nr_seryjny ?: 'Brak nr ser.',
                        'kod_wyrobu' => $termin->zlecenie->urzadzenie->kod_wyrobu,
                    ];
                }
            }
            $array[] = $item;
        }

        return response()->json([
            'date_string' => $date_string,
            'technik' => $user->technik->getArray(),
            'terminy' => $array,
        ], 200);
    }
}
