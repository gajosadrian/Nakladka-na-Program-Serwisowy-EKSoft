@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Wyszukiwanie części</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <form action="{{ route('zlecenia.wyszukiwanieCzesci') }}" method="get">
                    <b-row>
                        <b-col cols="7" lg="2">
                            <input name="symbol" type="text" class="form-control" value="{{ $symbol }}">

                            <div class="mt-2">
                                <b-badge variant="info">Symbol krótki</b-badge>
                                <b-badge variant="info">Symbol producenta</b-badge>
                            </div>
                        </b-col>
                        <b-col cols="5" lg="1">
                            <b-button type="submit" class="btn-rounded shadow" variant="info" size="sm">
                                <i class="fa fa-search"></i> Szukaj
                            </b-button>
                        </b-col>
                        <b-col cols="12" lg="3" class="mt-3">
                            @if ($towar and $towar->is_zdjecie)
                                <img src="{{ $towar->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                            @endif
                            <div class="font-w600">{{ $towar ? $towar->nazwa : '' }}</div>
                            <div>{{ $towar ? $towar->symbol_dostawcy : '' }}</div>
                        </b-col>
                    </b-row>
                </form>
            </template>
        </b-block>

        @if ($kosztorys_pozycje->count() > 0)
            <b-block full>
                <template slot="content">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover table-vcenter font-size-sm">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="font-w700">Lp.</th>
                                    <th class="font-w700">Nr zlecenia</th>
                                    <th class="font-w700">Symbol</th>
                                    <th class="font-w700">Symbol dost.</th>
                                    <th class="font-w700">Opis</th>
                                    <th class="font-w700">Cena</th>
                                    <th class="font-w700">Ilość</th>
                                    <th class="font-w700">Wartość netto</th>
                                    <th class="font-w700">Wartość brutto</th>
                                    <th class="font-w700">Przyjęcie</th>
                                    <th class="font-w700">Status</th>
                                    <th class="font-w700">Rozliczone</th>
                                    <th class="font-w700">Ostatnia data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kosztorys_pozycje as $key => $kosztorys_pozycja)
                                    <tr>
                                        <th>{{ $key + 1 }}</th>
                                        {!! $kosztorys_pozycja->zlecenie->table_cell_nr_html !!}
                                        <td>{{ $kosztorys_pozycja->symbol }}</td>
                                        <td nowrap>{{ $kosztorys_pozycja->symbol_dostawcy }}</td>
                                        <td class="text-danger" nowrap>
                                            @if ($kosztorys_pozycja->naszykowana_czesc)
                                                @if ($kosztorys_pozycja->naszykowana_czesc->sprawdzone_at or ($kosztorys_pozycja->zlecenie->is_odplatne and $kosztorys_pozycja->naszykowana_czesc->ilosc_do_zwrotu == 0))
                                                    <i
                                                        class="fa fa-check-circle text-success"
                                                        data-toggle="tooltip" data-placement="left"
                                                        @if ($kosztorys_pozycja->naszykowana_czesc->sprawdzone_at)
                                                            title="Sprawdzone {{ $kosztorys_pozycja->naszykowana_czesc->user_sprawdzil ? "({$kosztorys_pozycja->naszykowana_czesc->user_sprawdzil->name}, {$kosztorys_pozycja->naszykowana_czesc->sprawdzone_at->format('Y-m-d H:i')})" : '' }}"
                                                        @else
                                                            title="Nie wymaga zwrotu"
                                                        @endif
                                                    ></i>
                                                @else
                                                    <i
                                                        class="fa fa-minus-circle text-danger"
                                                        data-toggle="tooltip" data-placement="left"
                                                        title="Część nie została sprawdzona/odebrana"
                                                    ></i>
                                                @endif
                                            @else
                                                <i
                                                    class="fa fa-question-circle text-muted"
                                                    data-toggle="tooltip" data-placement="left"
                                                    title="Część nie była naszykowana"
                                                ></i>
                                            @endif
                                            {{ $kosztorys_pozycja->opis }}
                                        </td>
                                        <td nowrap>{{ $kosztorys_pozycja->cena_formatted }}</td>
                                        <td>{{ $kosztorys_pozycja->ilosc }}</td>
                                        <td nowrap>{{ $kosztorys_pozycja->wartosc_formatted }}</td>
                                        <td nowrap>{{ $kosztorys_pozycja->wartosc_brutto_formatted }}</td>
                                        <td>{{ $kosztorys_pozycja->zlecenie->data_przyjecia_formatted }}</td>
                                        {!! $kosztorys_pozycja->zlecenie->table_cell_status_html !!}
                                        <td class="table-{{ $kosztorys_pozycja->zlecenie->is_rozliczenie ? 'success' : 'danger' }}" nowrap>
                                            @if ($kosztorys_pozycja->zlecenie->is_rozliczenie)
                                                <i class="fa fa-check text-success mx-2"></i>
                                                {{ $kosztorys_pozycja->zlecenie->rozliczenie->rozliczenie->nr }}
                                            @else
                                                <i class="fa fa-times text-danger mx-2"></i>
                                                Nie
                                            @endif
                                        </td>
                                        <td>{{ $kosztorys_pozycja->zlecenie->data_zakonczenia_formatted }}</td>
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
