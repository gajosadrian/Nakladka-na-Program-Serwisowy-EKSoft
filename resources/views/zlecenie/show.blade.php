@extends('global.app', [ 'window' => true ])

@section('content')
    <b-container>
        <div class="row">
                        <div class="col-md-6">
                            <div class="block">
                                <div class="block-header">
                                    <h3 class="block-title">Title <small>Subtitle</small></h3>
                                </div>
                                <div class="block-content">
                                    <p>Simple block..</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="block">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Title <small>Subtitle</small></h3>
                                </div>
                                <div class="block-content">
                                    <p>With header background..</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="block block-bordered">
                                <div class="block-header">
                                    <h3 class="block-title">Title <small>Subtitle</small></h3>
                                </div>
                                <div class="block-content">
                                    <p>Bordered block..</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="block block-bordered">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Title <small>Subtitle</small></h3>
                                </div>
                                <div class="block-content">
                                    <p>Bordered block with header background..</p>
                                </div>
                            </div>
                        </div>
                    </div>
        <b-row>
            <b-col lg="6">
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
                        </table>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="6">
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
                            <b-col lg="6"></b-col>
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
    </b-container>
@endsection
