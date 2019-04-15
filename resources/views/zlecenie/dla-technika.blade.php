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
        <b-block title="Wybierz technika" full class="d-print-none">
            {{-- <template slot="options">
                @if ($technik)
                    <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                        <i class="fa fa-print"></i> Drukuj
                    </b-button>
                @endif
            </template> --}}
            <template slot="content">
                <div class="mb-3">
                    @foreach ($technicy as $_technik)
                        <b-link href="{{ route('zlecenia.dla-technika', [ 'technik_id' => $_technik->id, 'timestamp' => $timestamp ]) }}" class="btn btn-outline-primary {{ ($_technik->id == $technik_id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                    @endforeach
                </div>

                <b-row>
                    @if ($technik)
                        <b-col cols="2">
                            <input type="text" class="js-datepicker form-control" value="{{ $date_string }}" onchange="updateUrl(this)">
                        </b-col>
                        <b-col cols="2">
                            <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                                <i class="fa fa-print"></i> Drukuj
                            </b-button>
                        </b-cols>
                    @endif
                </b-row>
            </template>
        </b-block>

        @if ($technik)
            <b-block title="Zlecenia" noprint>
                <template slot="content">
                    <div class="mx-3" style="font-size: 1.1em">
                        <div class="mb-3 clearfix" style="font-size: 2.3em">
                            {{ $technik->nazwa }} {{ $date_formatted }}
                            <b-img src="{{ asset('media/dargaz-logo.png') }}" class="float-right" alt="logo"></b-img>
                        </div>
                        @foreach ($zlecenia as $zlecenie)
                            <div class="mb-4">
                                @if ($zlecenie->terminarz->temat)
                                    <div class="font-w700 bg-gray">
                                        <span class="{{ (strlen($zlecenie->terminarz->temat) <= 40) ? 'bg-dark text-white' : '' }} px-1">{{ $zlecenie->terminarz->temat }}</span>
                                    </div>
                                @endif
                                <div class="font-w700 bg-gray p-1">
                                    <b-row>
                                        <b-col cols="2">
                                            {{ $zlecenie->nr }}
                                        </b-col>
                                        <b-col cols="6">
                                            <i class="{{ $zlecenie->znacznik->icon }}"></i> {{ $zlecenie->znacznik_formatted }} {{ $zlecenie->nr_obcy ? ('| ' . $zlecenie->nr_obcy) : '' }}
                                        </b-col>
                                        <b-col cols="4" class="text-right">
                                            @if ($zlecenie->status->id == App\Models\Zlecenie\Status::NA_WARSZTACIE_ID)
                                                <span class="bg-dark text-white px-1">warsztat</span>
                                            @endif
                                            {{ $zlecenie->terminarz->godzina_rozpoczecia }} - {{ $zlecenie->terminarz->przeznaczony_czas_formatted }}
                                        </b-col>
                                    </b-row>
                                </div>
                                <div>
                                    <b-row>
                                        <b-col cols="6">
                                            <span class="font-w700">{{ $zlecenie->klient->symbol }} <u>{{ $zlecenie->klient->nazwa }}</u></span><br>
                                            {{ $zlecenie->klient->adres }}, {{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}<br>
                                            {{ $zlecenie->klient->telefony_formatted }}
                                        </b-col>
                                        <b-col cols="4">
                                            <span class="font-w700">{{ $zlecenie->urzadzenie->nazwa }}, {{ $zlecenie->urzadzenie->producent }}<br></span>
                                            <span class="font-w700">Model:</span> {!! $zlecenie->urzadzenie->model ?: '<span class="bg-gray font-w700 px-1">uzupełnić:</span>' !!}<br>
                                            <span class="font-w700">Nr ser.:</span> {!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="bg-gray font-w700 px-1">uzupełnić:</span>' !!}
                                        </b-col>
                                        <b-col cols="2">
											<div class="text-right">
												Zabudowa: [   ]<br>
												Trudna: [   ]
											</div>
										</b-col>
                                    </b-row>
                                </div>
                                <div class="mt-3">
                                    <b-row>
                                        <b-col cols="7" style="border-right: 1px solid #aaa">
											{{-- <div class="font-w700 text-uppercase">Opis zlecenia:</div> --}}
											<hr class="m-0" style="border-top-color: #aaa">
                                            <div class="py-2">
                                                {!! $zlecenie->opis_formatted !!}
                                            </div>
                                        </b-col>
                                        <b-col cols="5">
                                            <div class="clearfix" style="min-height: 170px">
                                                <div class="font-w700 text-uppercase">Uwagi technika:</div>
                                            </div>
                                        </b-col>
                                    </b-row>
                                </div>
                                @if ($zlecenie->kosztorys_pozycje->count() > 0)
                                    <div class="mt-2">
                                        <table class="table table-sm table-striped table-vcenter font-size-sm">
                                            <thead>
                                                <tr>
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
                                                        <td nowrap>{{ $pozycja->symbol_dostawcy }}</td>
                                                        <td nowrap>{{ $pozycja->symbol }}</td>
                                                        <td nowrap>{{ $pozycja->nazwa }}</td>
                                                        <td nowrap>{{ $pozycja->opis }}</td>
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
                                                    <th class="text-right">{{ number_format($wartosc_brutto, 2, '.', ' ') }} zł</th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
        technik_id: {{ $technik_id }},
        timestamp: Date.parse(value) / 1000,
    }));
}

</script>@endsection
