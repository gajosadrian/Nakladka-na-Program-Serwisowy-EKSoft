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
                    <div class="mx-3" style="font-family: 'Times New Roman', Times, serif">
                        <div class="mb-3" style="font-size: 3em">{{ $technik->nazwa }} {{ $date_formatted }}</div>
                        @foreach ($zlecenia as $zlecenie)
                            <div class="mb-4">
                                <div class="font-w700 bg-gray-light p-2">
                                    <b-row>
                                        <b-col cols="2">
                                            {{ $zlecenie->nr_or_obcy }}
                                        </b-col>
                                        <b-col cols="4">
                                            <i class="{{ $zlecenie->znacznik->icon }}"></i> {{ $zlecenie->znacznik_formatted }}
                                        </b-col>
                                        <b-col cols="6" class="text-right">
                                            {{ $zlecenie->terminarz->godzina_rozpoczecia }} - {{ $zlecenie->terminarz->przeznaczony_czas_formatted }}
                                        </b-col>
                                    </b-row>
                                </div>
                                <div>
                                    <b-row>
                                        <b-col cols="6">
                                            <span class="font-w700">{{ $zlecenie->klient->symbol }} <u>{{ $zlecenie->klient->nazwa }}</u></span><br>
                                            {{ $zlecenie->klient->adres }}, {{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}<br>
                                            {{ $zlecenie->klient->telefon }}
                                        </b-col>
                                        <b-col cols="6">
                                            <span class="font-w700">{{ $zlecenie->urzadzenie->nazwa }}, {{ $zlecenie->urzadzenie->producent }}<br></span>
                                            {!! $zlecenie->urzadzenie->model ?: '<span class="font-w700"><u>uzupełnić model:</u></span>' !!}<br>
                                            {!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="font-w700"><u>uzupełnić nr seryjny:</u></span>' !!}
                                        </b-col>
                                    </b-row>
                                </div>
                                <div class="mt-3">
                                    <b-row>
                                        <b-col cols="6" style="border-right: 1px solid #aaa">
                                            <div class="font-w700 text-uppercase">Opis zlecenia:</div>
                                            <div class="py-2">
                                                {!! $zlecenie->opis_formatted !!}
                                            </div>
                                        </b-col>
                                        <b-col cols="6">
                                            <div style="min-height: 200px">
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
                                                    <th class="font-w700 text-right" nowrap>Cena netto</th>
                                                    <th class="font-w700 text-center" nowrap>Ilość</th>
                                                    <th class="font-w700 text-right" nowrap>Wartość netto</th>
                                                    <th class="font-w700 text-right" nowrap>Wartość brutto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $wartosc_netto = 0.00;
                                                    $wartosc_brutto = 0.00;
                                                @endphp
                                                @foreach ($zlecenie->kosztorys_pozycje as $pozycja)
                                                    @php
                                                        $wartosc_netto += $pozycja->wartosc;
                                                        $wartosc_brutto += $pozycja->wartosc_brutto;
                                                    @endphp
                                                    <tr>
                                                        <td nowrap>{{ $pozycja->symbol_dostawcy }}</td>
                                                        <td nowrap>{{ $pozycja->symbol }}</td>
                                                        <td nowrap>{{ $pozycja->nazwa }}</td>
                                                        <td class="small" nowrap>{{ $pozycja->opis }}</td>
                                                        <td class="text-right" nowrap>{{ $pozycja->cena_formatted }}</td>
                                                        <td class="text-center" nowrap>{!! $pozycja->ilosc > 1 ? ('<strong><u>' . $pozycja->ilosc . '</u></strong>') : $pozycja->ilosc !!}</td>
                                                        <td class="text-right" nowrap>{{ $pozycja->wartosc_formatted }}</td>
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
                                                    <th class="text-right">{{ number_format($wartosc_netto, 2, '.', ' ') }} zł</th>
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
