@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zlecenia dla technika</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full class="d-print-none">
            <template slot="content">
                @if (! $is_technik)
                    <div class="mb-3">
                        @foreach ($technicy as $_technik)
                            <b-link href="{{ route('zlecenia.dla-technika', [ 'technik_id' => $_technik->id, 'timestamp' => $timestamp ]) }}" class="btn btn-outline-primary {{ ($_technik->id == $technik_id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                        @endforeach
                    </div>
                @endif

                <b-row>
                    @if ($technik)
                        <b-col cols="2">
                            <input type="text" class="js-datepicker form-control" value="{{ $date_string }}" onchange="updateUrl(this)">
                        </b-col>
                        <b-col cols="2">
                            <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                                <i class="fa fa-print"></i> Drukuj
                            </b-button>
                        </b-col>
                    @endif
                </b-row>
            </template>
        </b-block>

        @if ($technik)
            <b-block title="Zlecenia" noprint>
                <template slot="content">
                    <div class="" style="font-size: 1.1em">
                        <div class="mb-3 clearfix" style="font-size: 2.3em">
                            <div class="float-left">
                                {{ $technik->nazwa }} {{ $date_formatted }}
                            </div>
                            <div class="float-right">
                                <b-img src="{{ asset('media/dargaz-logo.png') }}" alt="logo"></b-img>
                            </div>
                        </div>

						<div class="mb-3">
							<span class="px-2">
								<span class="font-w700">Samochód:</span> {{ $samochod['value'][0] }}
							</span>
							@foreach ($terminarz_notatki as $terminarz_notatka)
								<span class="px-2 mr-2 bg-dark text-white">
									{{ $terminarz_notatka->temat }}
								</span>
							@endforeach
						</div>

                        @foreach ($terminy as $terminarz)
                            @php
                                $zlecenie = $terminarz->zlecenie;
                            @endphp
                            <div class="mb-4">
                                @if ($zlecenie->id)
                                    @if (!$is_technik and !$zlecenie->is_zakonczone and !$zlecenie->is_warsztat and !$zlecenie->_do_wyjasnienia and $is_up_to_date)
                                        <div class="d-none d-lg-block mb-1">
                                            @if ($terminarz->is_umowiono or !$terminarz->is_umowiono_or_dzwonic)
                                                <b-button @if(!$zlecenie->is_umowiono) onclick="umowKlienta({{ $zlecenie->id }}, 0)" @endif size="sm" variant="{{ $zlecenie->is_umowiono ? 'danger' : 'outline-danger' }}">Umówiono klienta</b-button>
                                            @endif
                                            @if ($terminarz->is_dzwonic or !$terminarz->is_umowiono_or_dzwonic)
                                                <b-button @if(!$zlecenie->is_umowiono) onclick="umowKlienta({{ $zlecenie->id }}, 1)" @endif size="sm" variant="{{ $zlecenie->is_umowiono ? 'primary' : 'outline-primary' }}">Umówiono klienta i dzwonić wcześniej</b-button>
                                            @endif
                                            @if ($zlecenie->is_gotowe)
                                                <b-button onclick="nieOdbiera({{ $zlecenie->id }})" size="sm" variant="{{ $zlecenie->is_recently_nie_odbiera ? 'warning' : 'outline-warning' }}">
                                                    Nie odbiera
                                                    @if ($zlecenie->last_status_nie_odbiera)
                                                        {{ $zlecenie->last_status_nie_odbiera->data_formatted }}
                                                    @endif
                                                </b-button>
                                            @endif
                                        </div>
                                    @endif

                                    @if ($zlecenie->_do_wyjasnienia)
                                        <div class="font-w700 bg-dark text-white">
                                            <span class="px-1">DO WYJAŚNIENIA</span>
                                        </div>
                                    @elseif ($terminarz->temat)
                                        <div class="font-w700 bg-gray">
                                            <span class="{{ (strlen($terminarz->temat) <= 40) ? 'bg-dark text-white' : '' }} px-1">{{ $terminarz->temat }}</span>
                                        </div>
                                    @endif
                                    <div class="font-w700 bg-gray p-1">
                                        <b-row class="gutters-tiny">
                                            <b-col cols="2">
                                                {{ $zlecenie->nr }}
                                            </b-col>
                                            <b-col cols="6">
                                                <i class="{{ $zlecenie->znacznik->icon }}"></i> {{ $zlecenie->znacznik_formatted }} {{ $zlecenie->nr_obcy ? ('| ' . $zlecenie->nr_obcy) : '' }}
                                            </b-col>
                                            @if (!$zlecenie->_do_wyjasnienia)
                                                <b-col cols="4" class="text-right">
                                                    @if ($zlecenie->is_warsztat)
                                                        <span class="bg-dark text-white px-1">warsztat</span>
                                                    @endif
                                                    {{ $terminarz->godzina_rozpoczecia }} - {{ $terminarz->przeznaczony_czas_formatted }}
                                                </b-col>
                                            @endif
                                        </b-row>
                                    </div>
                                    <div>
                                        <b-row>
                                            <b-col cols="7">
                                                <div class="clearfix">
                                                    <div class="float-left">
                                                        @if(!$zlecenie->is_warsztat and !$zlecenie->_do_wyjasnienia)
                                                            <i class="{{ $zlecenie->is_umowiono ? 'fa fa-check-circle' : 'far fa-circle' }}"></i>
                                                        @endif
                                                        <span class="font-w700 d-lg-none">{{ $zlecenie->klient->symbol }} <u>{{ $zlecenie->klient->nazwa }}</u></span>
                                                        <a class="font-w700 d-none d-lg-inline" href="javascript:void(0)" onclick="{{ $zlecenie->popup_link }}">{{ $zlecenie->klient->symbol }} {{ $zlecenie->klient->nazwa }}</a>
                                                        <br>
                                                        {{ $zlecenie->klient->adres }}, {{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}<br>
                                                        {{ $zlecenie->klient->telefony_formatted }}
                                                    </div>
                                                    <div class="float-right text-right">
                                                        @if (!$zlecenie->is_warsztat and !$zlecenie->_do_wyjasnienia)
                                                            <b-img src="https://chart.googleapis.com/chart?chs=80x80&cht=qr&chld=L|1&choe=UTF-8&chl={{ urlencode($zlecenie->google_maps_route_link) }}" fuild></b-img>
                                                        @endif
                                                    </div>
                                                </div>
                                            </b-col>
                                            <b-col cols="5">
                                                <span class="font-w700">{{ $zlecenie->urzadzenie->nazwa }}, {{ $zlecenie->urzadzenie->producent }}<br></span>
                                                <span class="font-w700">Model:</span> <span style="font-family: consolas;">{!! $zlecenie->urzadzenie->model ?: '<span class="bg-gray font-w700 px-1">uzupełnić:</span>' !!}</span><br>
                                                <span class="font-w700">Nr ser.:</span> <span style="font-family: consolas;">{!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="bg-gray font-w700 px-1">uzupełnić:</span>' !!}</span>
                                            </b-col>
                                        </b-row>
                                    </div>
                                    <div class="mt-3">
                                        <b-row>
                                            <b-col cols="7" style="border-right: 1px solid #aaa">
    											{{-- <div class="font-w700">OPIS ZLECENIA:</div> --}}
    											<hr class="m-0" style="border-top-color: #aaa">
                                                <div class="py-2">
                                                    {!! $zlecenie->opis_formatted !!}
                                                </div>
                                            </b-col>
                                            <b-col cols="5">
                                                <div class="clearfix" style="min-height: 170px">
                                                    <div class="float-left">
                                                        <div class="font-w700">UWAGI TECHNIKA:</div>
                                                    </div>
        											<div class="float-right text-right">
                                                        @if (!$zlecenie->is_odplatne and !$zlecenie->_do_wyjasnienia)
            												Zabudowa: [   ]<br>
            												Trudna: [   ]
                                                        @endif
        											</div>
                                                </div>
                                            </b-col>
                                        </b-row>
                                    </div>
                                    <div style="font-size:0.6em">
                                        <div class="d-none">
                                            Nie odbiera tel.:
                                        </div>
                                        <div>
                                            <span style="border-top: 1px solid #aaa;">
                                                Przyjął: {{ $zlecenie->przyjmujacy->nazwa }}
                                                @if ($zlecenie->is_umowiono)
                                                    ◦◦ Umówił: {{ $zlecenie->last_status_umowiono->pracownik->nazwa }} {{ $zlecenie->last_status_umowiono->data->format('m.d H:i') }}
                                                @endif
                                                @if (true)
                                                    ◦◦ Trwanie zlecenia: {{ $zlecenie->czas_oczekiwania_formatted }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @if ($zlecenie->kosztorys_pozycje->count() > 0)
                                        <div class="mt-2">
                                            <table class="table table-sm table-striped table-vcenter font-size-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="font-w700" nowrap>Półka</th>
                                                        <th class="font-w700" nowrap>Symbol dost.</th>
                                                        <th class="font-w700" nowrap>Symbol</th>
                                                        <th class="font-w700" nowrap>Nazwa</th>
                                                        <th class="font-w700" nowrap>Opis</th>
                                                        <th class="font-w700 text-right" nowrap>Cena brutto</th>
                                                        <th class="font-w700 text-center" nowrap>Ilość</th>
                                                        <th class="font-w700 text-right" nowrap>Wartość brutto</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $wartosc_brutto = 0.00;
                                                    @endphp
                                                    @foreach ($zlecenie->kosztorys_pozycje as $pozycja)
                                                        @php
                                                            $wartosc_brutto += $pozycja->wartosc_brutto;
                                                        @endphp
                                                        <tr>
                                                            <td nowrap>{{ $pozycja->polka }}</td>
                                                            <td nowrap>{{ $pozycja->symbol_dostawcy }}</td>
                                                            <td nowrap>{{ $pozycja->symbol }}</td>
                                                            <td nowrap>{{ str_limit($pozycja->nazwa, 30) }}</td>
                                                            <td nowrap>{{ str_limit($pozycja->opis_fixed, 15) }}</td>
                                                            <td class="text-right" nowrap>{{ $pozycja->cena_brutto_formatted }}</td>
                                                            <td class="text-center" nowrap>{!! $pozycja->ilosc != 1 ? ('<span class="bg-gray font-w700 px-1">' . $pozycja->ilosc . '</span>') : $pozycja->ilosc !!}</td>
                                                            <td class="text-right" nowrap>{{ $pozycja->wartosc_brutto_formatted }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        <th class="text-right">{{ number_format($wartosc_brutto, 2, '.', ' ') }} zł</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @endif
                                @else
                                    <div class="font-w700 bg-gray p-1">
                                        <b-row class="gutters-tiny">
                                            <b-col cols="9">
                                                {{ $terminarz->temat }}
                                            </b-col>
                                            <b-col cols="3" class="text-right">
                                                {{ $terminarz->godzina_rozpoczecia }} - {{ $terminarz->przeznaczony_czas_formatted }}
                                            </b-col>
                                        </b-row>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('zlecenia.dla-technika', {
        technik_id: @json($technik_id),
        timestamp: Date.parse(value) / 1000,
    }));
}

function umowKlienta(zlecenie_id, dzwonic_wczesniej = 0) {
    $.post(route('zlecenia.api.umow_klienta', { id: zlecenie_id }), {
        '_token': '{{ csrf_token() }}',
        dzwonic_wczesniej: dzwonic_wczesniej
    })
        .done(function (data) {
            location.reload();
        });
}

function nieOdbiera(zlecenie_id) {
    $.post(route('zlecenia.api.nie_odbiera', { id: zlecenie_id }), {
        '_token': '{{ csrf_token() }}'
    })
        .done(function (data) {
            location.reload();
        });
}

@if ($technik)
    var last_terminarz_statusy = null;
    function refreshIfNew()
    {
        $.get(route('zlecenia.api.terminarz_statusy', { technik_id: @json($technik->id), date_string: @json($date_string) }), {
            '_token': '{{ csrf_token() }}'
        })
            .done(function (data) {
                if (last_terminarz_statusy) {
                    if (! isEqual(last_terminarz_statusy, data)) {
                        location.reload();
                    }
                }
                last_terminarz_statusy = data;
            });
    }
    setInterval(refreshIfNew, 15000);
@endif

</script>@endsection
