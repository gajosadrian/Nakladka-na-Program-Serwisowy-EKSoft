@extends('global.app')
@php
    $room = rand();
@endphp

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rozliczenie nr <span class="font-w600">{{ $rozliczenie->nr }}</span> - Analiza</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Wybierz zleceniodawcę" full>
            <template slot="content">
                <b-button-group>
                    @foreach ($zleceniodawcy as $_zleceniodawca)
                        <b-link href="{{ route('rozliczenia.analiza', [ 'id' => $rozliczenie->id, 'zleceniodawca' => $_zleceniodawca ]) }}" class="btn btn-outline-primary {{ ($_zleceniodawca == $zleceniodawca) ? 'active' : '' }}">{{ $_zleceniodawca }}</b-link>
                    @endforeach
                </b-button-group>
            </template>
        </b-block>

        @if ($is_zleceniodawca)
            <b-block title="{{ $zleceniodawca }}" full>
                <template slot="content">
                    <table id="zlecenia{{ $room }}" class="table table-striped table-hover table-borderless table-vcenter font-size-sm dataTable">
                        <thead>
                            <th class="font-w700">Lp.</th>
                            <th class="font-w700">Nr zlecenia</th>
                            <th class="font-w700">Zleceniodawca</th>
                            <th class="font-w700">Robocizny</th>
                            <th class="font-w700">Dojazdy</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="d-none"></th>
                        </thead>
                        <tbody>
                            @foreach ($zlecenia as $index => $rozliczone_zlecenie)
                                @php
                                    $zlecenie = $rozliczone_zlecenie->zlecenie;
                                @endphp
                                <tr class="table-secondary">
                                    <th>{{ $index + 1 }}</th>
                                    {!! $zlecenie->tableCellNrHTML !!}
                                    <td>{{ $rozliczone_zlecenie->zleceniodawca }}</td>
                                    <td>{!! $rozliczone_zlecenie->robocizny_html !!}</td>
                                    <td>{!! $rozliczone_zlecenie->dojazdy_html !!}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="d-none">
                                        {{ $zlecenie->nr }} ; {{ $zlecenie->nr_obcy }}
                                    </td>
                                </tr>
                                @foreach ($zlecenie->kosztorys_pozycje as $index2 => $pozycja)
                                    @if ($pozycja->is_czesc)
                                        @if ($index2 == 0)
                                            <tr id="noclicable">
                                                <th>{{ $index + 1 }}.{{ $index2 }}</th>
                                                <th>Symbol</th>
                                                <th>Symbol dost.</th>
                                                <th>Nazwa</th>
                                                <th class="text-right">Cena netto</th>
                                                <th class="text-center">Ilość</th>
                                                <th class="text-right">Wartość netto</th>
                                                <th class="text-right">Wartość brutto</th>
                                                <th class="d-none"></th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>{{ $index + 1 }}.{{ $index2 + 1 }}</th>
                                            <td>{{ $pozycja->towar->symbol }}</td>
                                            <td>{{ $pozycja->towar->symbol_dostawcy }}</td>
                                            <td>{{ $pozycja->towar->nazwa }} {{ $pozycja->opis ? '-' : '' }} <span class="text-danger">{{ $pozycja->opis }}</span></td>
                                            <td class="text-right">{{ $pozycja->cena_formatted }}</td>
                                            <td class="text-center {{ $pozycja->ilosc > 1 ? 'font-w600 text-danger' : '' }}">{{ $pozycja->ilosc }}</td>
                                            <th class="text-right">{{ $pozycja->wartosc_formatted }}</th>
                                            <th class="text-right">{{ $pozycja->wartosc_brutto_formatted }}</th>
                                            <td class="d-none">
                                                {{ $zlecenie->nr }} ; {{ $zlecenie->nr_obcy }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>$(function(){
    var $lastRow = null;

    $('table#zlecenia{{ $room }} > tbody tr:not(#noclicable)').click(function () {
        let $row = $(this);

        if ($lastRow) {
            $lastRow.removeClass('table-danger');
        }
        $row.addClass('table-danger');

        $lastRow = $row;
    });
})</script>@endsection
