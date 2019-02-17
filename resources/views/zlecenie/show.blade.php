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
                                <td>{{ $zlecenie->klient_id }}</td>
                            </tr>
                            <tr>
                                <th>Ulica:</th>
                                <td>{{ $zlecenie->klient_id }}</td>
                            </tr>
                            <tr>
                                <th>Miasto:</th>
                                <td>{{ $zlecenie->klient_id }}</td>
                            </tr>
                            <tr>
                                <th>Telefony:</th>
                                <td>{{ $zlecenie->klient_id }}</td>
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
                                        <td>{{ $zlecenie->data_przyjecia }}</td>
                                    </tr>
                                    <tr>
                                        <th>Zakończenie:</th>
                                        <td>{{ $zlecenie->data_zakonczenia }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;od:</th>
                                        <td>{{ $zlecenie->terminarz->data_rozpoczecia }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;do:</th>
                                        <td>{{ $zlecenie->terminarz->data_zakonczenia }}</td>
                                    </tr>
                                </table>
                            </b-col>
                        </b-row>
                    </template>
                </b-block>
            </b-col>
        </b-row>
        <b-row>
            <b-col lg="12">
                <b-block title="Opis" full>
                    <template slot="content">
                        {!! $zlecenie->opisBr !!}
                    </template>
                </b-block>
            </b-col>
        </b-row>
    </div>
@endsection
