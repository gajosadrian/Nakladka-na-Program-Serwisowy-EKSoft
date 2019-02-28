@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="content">
        @if ($zlecenie->errors)
            <b-row>
                <b-col>
                    <div class="alert alert-danger d-flex align-items-center">
                        <div class="flex-00-auto">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-fill ml-3">
                            <p class="mb-0">{{ implode(', ', $zlecenie->errors) }}</p>
                        </div>
                    </div>
                </b-col>
            </b-row>
        @endif
        <b-row class="row-deck">
            <b-col lg="5">
                <b-block title="Kontrahent">
                    <template slot="content">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:1%">Nazwa:</th>
                                <td>{{ $zlecenie->klient->nazwa }} <span class="small text-muted">({{ $zlecenie->klient->symbol }})</span></td>
                            </tr>
                            <tr>
                                <th>Ulica:</th>
                                <td>-</td>
                            </tr>
                            <tr>
                                <th>Miasto:</th>
                                <td>00-000 -</td>
                            </tr>
                            <tr>
                                <th>Telefony:</th>
                                <td>-</td>
                            </tr>
                        </table>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="7">
                <b-block title="Dane zlecenia">
                    <template slot="content">
                        <b-row>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%">Numer:</th>
                                        <td>{{ $zlecenie->nr }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nr&nbsp;obcy:</th>
                                        <td>{{ $zlecenie->nr_obcy ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rodzaj:</th>
                                        <td><i class="{{ $zlecenie->znacznik->icon }}"></i> {{ $zlecenie->znacznik->nazwa }}</td>
                                    </tr>
                                    <tr>
                                        <th>Źródło:</th>
                                        <td><i class="{{ $zlecenie->zrodlo->icon }}"></i> {{ $zlecenie->zrodlo->nazwa }}</td>
                                    </tr>
                                </table>
                            </b-col>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%">Czas&nbsp;trwania:</th>
                                        <td>{{ $zlecenie->czas_trwania_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Przyjęcie:</th>
                                        <td>{{ $zlecenie->data_przyjecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Zakończenie:</th>
                                        <td>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;od:</th>
                                        <td>{{ $zlecenie->terminarz->data_rozpoczecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;do:</th>
                                        <td>{{ $zlecenie->terminarz->data_zakonczenia_formatted }}</td>
                                    </tr>
                                </table>
                            </b-col>
                        </b-row>
                    </template>
                </b-block>
            </b-col>
        </b-row>
        <b-row class="row-deck">
            <b-col lg="3">
                <b-block title="Urządzenie">
                    <template slot="content">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:1%">Nazwa:</th>
                                <td>{{ $zlecenie->urzadzenie->nazwa }}</td>
                            </tr>
                            <tr>
                                <th>Producent:</th>
                                <td>{{ $zlecenie->urzadzenie->producent }}</td>
                            </tr>
                            <tr>
                                <th>Model:</th>
                                <td>{!! $zlecenie->urzadzenie->model ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Kod&nbsp;wyrobu:</th>
                                <td>{!! $zlecenie->urzadzenie->kod_wyrobu ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Nr seryjny:</th>
                                <td>{!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                        </table>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="9">
                <div class="block block-rounded shadow-sm">
                    <ul class="nav nav-tabs nav-tabs-alt js-tabs block-header-default" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" href="#kosztorys">Kosztorys</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active show" href="#opis">Opis</a>
                        </li>
                    </ul>
                    <div class="block-content tab-content overflow-hidden block-content-full">
                        <div class="tab-pane fade" id="kosztorys" role="tabpanel">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Symbol dost.</th>
                                        <th>Symbol</th>
                                        <th>Nazwa</th>
                                        {{-- <th>Opis</th> --}}
                                        <th>Cena netto</th>
                                        <th>Ilość</th>
                                        <th>Wartość netto</th>
                                        <th>Wartość brutto</th>
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
                                            <td>{{ $pozycja->towar->symbol_dostawcy }}</td>
                                            <td>{{ $pozycja->towar->symbol }}</td>
                                            <td class="small align-middle">{{ $pozycja->towar->nazwa }}</td>
                                            {{-- <td class="small">{{ $pozycja->towar->opis }}</td> --}}
                                            <td>{{ $pozycja->cena }} zł</td>
                                            <td class="{{ $pozycja->ilosc > 1 ? 'font-w600 text-danger' : '' }}">{{ $pozycja->ilosc }}</td>
                                            <td>{{ $pozycja->wartosc }} zł</td>
                                            <td>{{ $pozycja->wartosc_brutto }} zł</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        {{-- <th></th> --}}
                                        <th></th>
                                        <th></th>
                                        <th>{{ $wartosc_netto }} zł</th>
                                        <th>{{ $wartosc_brutto }} zł</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane fade active show" id="opis" role="tabpanel">
                            <div class="mb-3">
                                {!! $zlecenie->opisBr !!}
                            </div>
                            <zlecenie-append-opis zlecenie_id=@json($zlecenie->id) />
                        </div>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
@endsection
