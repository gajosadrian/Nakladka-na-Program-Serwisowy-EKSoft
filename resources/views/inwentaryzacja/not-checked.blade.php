@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none d-none d-sm-block">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Inwentaryzacja - nie sprawdzone</h1>
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
                            Inwentaryzacja - nie sprawdzone
                        </div>
                        <div class="float-right">
                            <b-img src="{{ asset('media/dargaz-logo.png') }}" alt="logo"></b-img>
                        </div>
                    </div>

                    <table class="table table-sm table-vcenter">
                        <thead>
                            <tr class="text-uppercase">
                                <th class="font-w700" style="width:1%;">Lp.</th>
                                <th class="font-w700" style="width:1%;">Symbol</th>
                                <th class="font-w700" style="width:1%;">Półka</th>
                                <th class="font-w700" style="width:1%;">Nazwa</th>
                                <th class="font-w700" style="width:1%;" nowrap>Stan (Subiekt)</th>
                                <th class="font-w700" nowrap>Stan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 0;
                            @endphp
                            @foreach ($towary as $towar)
                                <tr>
                                    <td class="font-w600">{{ ++$i }}.</td>
                                    <td class="font-w600" nowrap>
                                        <i class="far fa-square mr-1"></i>
                                        {{ $towar->symbol }}
                                    </td>
                                    <td nowrap>{{ $towar->polka }}</td>
                                    <td nowrap>{{ str_limit($towar->nazwa, 40) }}</td>
                                    <td>{{ $towar->stan->stan }}</td>
                                    <td>
                                        <input type="text" class="form-control border border-danger" style="width:100px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </template>
        </b-block>
    </div>
@endsection
