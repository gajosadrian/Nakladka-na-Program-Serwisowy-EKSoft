@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Inwentaryzacja</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <form action="{{ route('inwentaryzacja.show') }}" method="get">
                    <b-row>
                        <b-col cols="12" lg="2">
                            <input name="symbol" type="text" class="form-control push" placeholder="Symbol" value="{{ $symbol }}" onclick="select()">
                        </b-col>
                        <b-col cols="12" lg="2">
                            <input name="polka" type="text" class="form-control push" placeholder="Półka" value="{{ $polka }}" onclick="select()">
                        </b-col>
                        <b-col cols="12" lg="2">
                            <input name="pojemnik" type="text" class="form-control push" placeholder="Pojemnik" value="{{ $pojemnik }}" onclick="select()">
                        </b-col>
                        <b-col cols="12">
                            <b-button type="submit" class="btn-rounded shadow" variant="info" size="sm">
                                <i class="fa fa-search"></i> Szukaj
                            </b-button>
                        </b-col>
                    </b-row>
                </form>
            </template>
        </b-block>

        @if ($towar)
            @if ( ! $valid_polka)
                <div class="alert alert-danger">
                    <p class="mb-0">Nieprawidłowa półka</p>
                </div>
            @endif

            <b-block title="Logi">
                <template slot="content">
                    <ul>
                        @foreach ($stan_logs as $stan_log)
                            @if ($stan_log->status == 'new')
                                <li><span class="font-w600 text-success">Dodanie stanu: {{ $stan_log->stan }}</span>, półka: {{ $stan_log->polka }}, {{ $stan_log->user->name }} {{ $stan_log->created_at }}</li>
                            @elseif ($stan_log->status == 'update')
                                <li><span class="font-w600 text-warning">Edycja stanu: {{ $stan_log->stan }}</span>, półka: {{ $stan_log->polka }}, {{ $stan_log->user->name }} {{ $stan_log->created_at }}</li>
                            @elseif ($stan_log->status == 'delete')
                                <li><span class="font-w600 text-danger">Usunięcie stanu: {{ $stan_log->stan }}</span>, półka: {{ $stan_log->polka }}, {{ $stan_log->user->name }} {{ $stan_log->created_at }}</li>
                            @endif
                        @endforeach
                    </ul>
                </template>
            </b-block>

            <b-block full>
                <template slot="content">

                    <b-row>
                        @if ($towar and $towar->is_zdjecie)
                            <b-col cols="12" lg="3" class="push">
                                <img src="{{ $towar->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                            </b-col>
                        @endif
                        <b-col cols="12" lg="9">
                            <div class="font-w600"><u>{{ $towar->nazwa }}</u></div>
                            <div>Symbol: <span>{{ $towar->symbol }}</span></div>
                            <div>Prawidłowa półka: <span class="font-w600 {{ $valid_polka ? 'text-success' : 'text-danger' }}">{{ $towar->polka }}</span></div>
                            <div class="mt-2">
                                <form class="form-inline" action="{{ route('inwentaryzacja.update', $towar->symbol) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="is_new" value="{{ $stan ? 0 : 1 }}">
                                    <input type="hidden" name="towar_id" value="{{ $towar->id }}">
                                    <input type="hidden" name="symbol" value="{{ $towar->symbol }}">
                                    <input type="hidden" name="polka" value="{{ $_polka ?? '' }}">
                                    <input type="number" name="stan" class="form-control mb-2 mr-sm-2 mb-sm-0" placeholder="Stan" value="{{ $stan->stan ?? '' }}">
                                    <button type="submit" class="btn btn-primary">Zapisz</button>
                                </form>
                            </div>
                        </b-col>
                    </b-row>

                </template>
            </b-block>
        @endif
    </div>
@endsection
