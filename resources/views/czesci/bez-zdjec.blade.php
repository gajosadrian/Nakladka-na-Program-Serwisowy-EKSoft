@php
    $is_mobile = auth()->user()->is_mobile;
@endphp

@extends('global.app', [ 'window' => $is_mobile ])

@section('content')
    <div class="bg-body-light d-print-none d-none d-sm-block">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Części bez zdjęć</h1>
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

        <b-block title="Części" noprint>
            <template slot="content">
                <div class="mx-3">
                    <div class="mb-3 clearfix" style="font-size: 2em">
                        <div class="float-left">
                            Części bez zdjęć
                        </div>
                        <div class="float-right">
                            <b-img src="{{ asset('media/dargaz-logo.png') }}" alt="logo"></b-img>
                        </div>
                    </div>

                    <table class="table table-sm table-striped font-size-sm">
                        <thead>
                            <tr class="text-uppercase">
                                <th class="font-w700" style="width: 50px;">Lp.</th>
                                <th class="font-w700" style="width: 100px;">Symbol</th>
                                <th class="font-w700" style="width: 100px;">Półka</th>
                                <th class="font-w700">Nazwa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($towary as $key => $towar)
                                <tr>
                                    <td>{{ $key+1 }}.</td>
                                    <th nowrap>{{ $towar->symbol }}</th>
                                    <td nowrap>{{ $towar->polka }}</td>
                                    <td nowrap>{{ $towar->nazwa }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </template>
        </b-block>
    </div>
@endsection
