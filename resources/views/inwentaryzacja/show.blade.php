@extends('global.app', [ 'window' => true ])

@section('content')
    {{-- <div class="bg-body-light d-print-none d-none d-sm-block">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Inwentaryzacja</h1>
            </div>
        </div>
    </div> --}}

    <div class="content">
        <b-block title="Parametry" theme="{{ $valid_polka ? '' : 'bg-danger' }}" full>
            <template slot="content">
                <form action="{{ route('inwentaryzacja.show') }}" method="get">
                    <b-row class="gutters-tiny">
                        <b-col cols="4" lg="2">
                            <input name="symbol" type="number" class="form-control push" placeholder="Symbol" value="{{ $symbol }}" onclick="select()" @if($stan) onfocus="select()" autofocus @endif>
                        </b-col>
                        <b-col cols="4" lg="2">
                            <input name="polka" type="text" class="form-control push" placeholder="Półka" value="{{ $polka }}">
                        </b-col>
                        <b-col cols="4" lg="2">
                            <input name="pojemnik" type="number" class="form-control push" placeholder="Pojemnik" value="{{ $pojemnik }}" onclick="select()">
                        </b-col>
                        <b-col cols="12" class="mt-2">
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

            @if ($is_stan_logs)
                <b-block title="Logi">
                    <template slot="content">
                        <ul>
                            <li class="font-w600 bg-info text-white px-1 d-inline">STAN OGÓŁEM: {{ $stany->sum('stan') }}</li>
                            {{-- <li class="font-w600 bg-warning text-white px-1 d-inline">STAN SUBIEKT: {{ $towar->ilosc }}</li> --}}
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
            @endif

            <b-block full>
                <template slot="content">

                    <b-row>
                        @if ($towar and $towar->is_zdjecie)
                            <b-col cols="8" lg="3" class="push">
                                <img src="{{ $towar->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                            </b-col>
                        @endif
                        <b-col cols="12" lg="9">
                            <div class="font-w600"><u>{{ $towar->nazwa }}</u></div>
                            <div>Symbol: <span>{{ $towar->symbol }}</span></div>
                            @if ($towar->polka)
                                <div>Prawidłowa półka: <span class="font-w600 {{ $valid_polka ? 'text-success' : 'text-danger' }}">{{ $towar->polka }}</span></div>
                            @else
                                <div class="font-w600 text-danger">Brak przypisanej półki</div>
                            @endif
                            <div class="mt-2">
                                <form class="form-inline" action="{{ route('inwentaryzacja.update', $towar->symbol) }}" method="post">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="is_new" value="{{ $stan ? 0 : 1 }}">
                                    <input type="hidden" name="towar_id" value="{{ $towar->id }}">
                                    <input type="hidden" name="symbol" value="{{ $towar->symbol }}">
                                    <input type="hidden" name="polka" value="{{ $_polka ?? '' }}">
                                    <input type="text" name="stan" class="form-control mb-2 mr-sm-2 mb-sm-0" placeholder="Stan" value="{{ $stan->stan ?? '' }}" @if( ! $stan) onfocus="select()" autofocus @endif>
                                    {{-- @if (!$valid_polka and (!$towar->polka or (str_contains($towar->polka, ['s', 'S']) and str_contains($_polka, ['m', 'M'])))) --}}
                                    @if (!$valid_polka and (!$towar->polka or str_contains($_polka, ['m', 'M'])))
                                        <input type="text" name="polka_new" class="form-control mb-2 mr-sm-2 mb-sm-0" placeholder="Półka" value="{{ $towar->polka }}">
                                    @endif
                                    <button type="submit" class="btn {{ $stan ? 'btn-warning' : 'btn-success' }}">{{ $stan ? 'Edytuj' : 'Dodaj' }}</button>
                                </form>
                            </div>
                        </b-col>
                    </b-row>

                </template>
            </b-block>
        @endif
    </div>
@endsection
