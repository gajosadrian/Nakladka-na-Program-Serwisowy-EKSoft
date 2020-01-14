@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none d-none d-sm-block">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Inwentaryzacja - różnice</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full class="d-print-none">
            <template slot="content">
                <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                    <i class="fa fa-print"></i> Drukuj
                </b-button>
            </template>
        </b-block>

        <b-block title="Zlecenia" noprint>
            <template slot="content">
                <div class="mx-3">
                    <div class="mb-3 clearfix" style="font-size: 2em">
                        <div class="float-left">
                            Inwentaryzacja - różnice
                        </div>
                        <div class="float-right">
                            <b-img src="{{ asset('media/dargaz-logo.png') }}" alt="logo"></b-img>
                        </div>
                    </div>

                    <table class="table table-vcenter">
                        <thead>
                            <tr class="text-uppercase">
                                <th class="font-w700" style="width:1%">Lp.</th>
                                <th class="font-w700" style="width:1%">Symbol</th>
                                <th class="font-w700" style="width:1%">Różnica</th>
                                <th class="font-w700" style="width:1%">Nazwa</th>
                                <th class="font-w700" style="width:1%" nowrap>Stan (Subiekt)</th>
                                <th class="font-w700" nowrap>Stan (Inwenta.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($stany_grouped as $stany)
                                @php
                                    $stan = $stany[0];
                                    $inwentaryzacja_stan = $stany->sum('stan');
                                    $stan_diff = $inwentaryzacja_stan - $stany[0]->towar->stan->stan;

                                    if ($stan_diff == 0) continue;
                                    if ($mode == 0 and $stan_diff < 0) continue;
                                    if ($mode == 1 and $stan_diff > 0) continue;
                                @endphp

                                <tr>
                                    <td class="font-w600">{{ ++$i }}.</td>
                                    <td class="font-w600" nowrap>
                                        <i class="far fa-square mr-1"></i>
                                        {{ $stan->towar->symbol }}
                                    </td>
                                    <td class="font-w600 font-w600 table-{{ ($stan_diff > 0) ? 'success' : 'danger' }}" nowrap>
                                        {{ ($stan_diff > 0) ? '+' : '' }}{{ $stan_diff }}
                                    </td>
                                    <td nowrap>{{ str_limit($stan->towar->nazwa, 30) }}</td>
                                    <td class="font-w600">{{ $stan->towar->stan->stan }}</td>
                                    <td class="font-w600">{{ $inwentaryzacja_stan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </template>
        </b-block>
    </div>
@endsection
