@extends('global.app', [ 'window' => true ])

@php
    $user = auth()->user();
@endphp

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
                <b-block title="Kontrahent" theme="bg-primary-light">
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
                <b-block title="Dane zlecenia" theme="bg-primary-light">
                    <template slot="content">
                        <b-row>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%">Numer:</th>
                                        <td>
                                            {{ $zlecenie->nr }}
                                            <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{{ $zlecenie->nr }}'">
                                                <i class="far fa-copy"></i>
                                            </a>
                                        </td>
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
                                    <tr>
                                        <th>Status:</th>
                                        <td class="align-middle {{ $zlecenie->status->color ? 'table-' . $zlecenie->status->color : '' }}">
                                            <i class="{{ $zlecenie->status->icon }} {{ $zlecenie->status->color ? 'text-' . $zlecenie->status->color : '' }} mx-2"></i>
    										{{ $zlecenie->status->nazwa }}
                                        </td>
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
                <b-block title="Urządzenie" theme="bg-primary-light">
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
                    <ul class="nav nav-tabs nav-tabs-alt align-items-center js-tabs bg-primary-light" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#kosztorys" class="nav-link" style="color: rgba(255, 255, 255, 0.9)">Kosztorys</a>
                        </li>
                        <li class="nav-item">
                            <a href="#opis" class="nav-link active show" style="color: rgba(255, 255, 255, 0.9)">Opis</a>
                        </li>
                        <li class="nav-item ml-auto">
                            <b-button-group size="sm" class="mr-2">
                                @if ($user->is_technik and $zlecenie->status->id != App\Zlecenie_Status::PREAUTORYZACJA_ID)
                                    <zlecenie-change-status
                                        zlecenie_id=@json($zlecenie->id)
                                        status_id=@json(App\Zlecenie_Status::PREAUTORYZACJA_ID)
                                        remove_termin="0"
                                        name=@json(App\Zlecenie_Status::getName(App\Zlecenie_Status::PREAUTORYZACJA_ID))
                                        icon=@json(App\Zlecenie_Status::getIcon(App\Zlecenie_Status::PREAUTORYZACJA_ID))
                                        color=@json(App\Zlecenie_Status::getColor(App\Zlecenie_Status::PREAUTORYZACJA_ID))></zlecenie-change-status>
                                @elseif (!$user->is_technik and $zlecenie->status->id == App\Zlecenie_Status::ZAMOWIONO_CZESC_ID)
                                    <zlecenie-change-status
                                        zlecenie_id=@json($zlecenie->id)
                                        status_id=@json(App\Zlecenie_Status::GOTOWE_DO_WYJAZDU_ID)
                                        remove_termin="1"
                                        name=@json(App\Zlecenie_Status::getName(App\Zlecenie_Status::GOTOWE_DO_WYJAZDU_ID))
                                        icon=@json(App\Zlecenie_Status::getIcon(App\Zlecenie_Status::GOTOWE_DO_WYJAZDU_ID))
                                        color=@json(App\Zlecenie_Status::getColor(App\Zlecenie_Status::GOTOWE_DO_WYJAZDU_ID))></zlecenie-change-status>
                                @endif
                            </b-button-group>
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
                            <zlecenie-opis zlecenie_id=@json($zlecenie->id) />
                        </div>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
@endsection
