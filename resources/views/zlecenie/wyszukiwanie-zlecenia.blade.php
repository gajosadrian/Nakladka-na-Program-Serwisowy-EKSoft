@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Wyszukiwanie zleceń</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <form action="{{ route('zlecenia.wyszukiwanieZlecenia') }}" method="get">
                    <b-row>
                        <b-col cols="7" lg="2">
                            <input name="search" type="text" class="form-control" value="{{ $search ?? '' }}">
                        </b-col>
                        <b-col cols="5" lg="1">
                            <b-button type="submit" class="btn-rounded shadow" variant="info" size="sm">
                                <i class="fa fa-search"></i> Szukaj
                            </b-button>
                        </b-col>
                        <b-col cols="12" lg="9">
                            <b-badge variant="info">Kod klienta</b-badge>
                            <b-badge variant="info">Imię i nazwisko</b-badge>
                            <b-badge variant="info">Miejscowość</b-badge>
                            <b-badge variant="info">Adres</b-badge>
                            <b-badge variant="info">Nr zlecenia</b-badge>
                            <b-badge variant="info">Opis zlecenia</b-badge>
                            <b-badge variant="info">Marka</b-badge>
                            <b-badge variant="info">Nr seryjny</b-badge>
                        </b-col>
                    </b-row>
                </form>
            </template>
        </b-block>

        @if ($search)
            <b-block full>
                <template slot="content">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover table-vcenter font-size-sm">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="font-w700" style="width:1%">Lp.</th>
                                    <th class="font-w700">Nazwa</th>
                                    <th class="font-w700">Adres</th>
                                    <th class="font-w700">Nr zlecenia</th>
                                    <th class="font-w700">Przyjęcie</th>
                                    <th class="font-w700">Status</th>
                                    <th class="font-w700">Rozliczone</th>
                                    <th class="font-w700" nowrap>Ostatnia data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zlecenia as $key => $zlecenie)
                                    <tr>
                                        <th>{{ $key + 1 }}</th>
                                        <td nowrap>
                                            {{ str_limit($zlecenie->klient->nazwa, 30) }}<br>
                                            <small class="text-muted">({{ $zlecenie->klient->symbol }})</small>
                                        </td>
                                        <td nowrap>
    										{{ $zlecenie->klient->adres }}<br>
    										{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}
    									</td>
                                        {!! $zlecenie->table_cell_nr_html !!}
                                        <td nowrap>{{ $zlecenie->data_przyjecia_formatted }}</td>
                                        {!! $zlecenie->table_cell_status_html !!}
                                        <td class="table-{{ $zlecenie->is_rozliczenie ? 'success' : 'danger' }}" nowrap>
                                            @if ($zlecenie->is_rozliczenie)
                                                <i class="fa fa-check text-success mx-2"></i>
                                                {{ $zlecenie->rozliczenie->rozliczenie->nr }}
                                            @else
                                                <i class="fa fa-times text-danger mx-2"></i>
                                                Nie
                                            @endif
                                        </td>
                                        <td nowrap>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </template>
            </b-block>
        @endif
    </div>
@endsection
