@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Wyszukiwanie zlecenia</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <form action="{{ route('zlecenia.wyszukiwanieZlecenia') }}" method="get">
                    <b-row>
                        <b-col cols="7" lg="2">
                            <input name="nr_zlec" type="text" class="form-control" value="{{ $nr_zlec ?? '' }}">
                        </b-col>
                        <b-col cols="5" lg="1">
                            <b-button type="submit" class="btn-rounded shadow" variant="info" size="sm">
                                <i class="fa fa-search"></i> Szukaj
                            </b-button>
                        </b-col>
                    </b-row>
                </form>
            </template>
        </b-block>

        @if ($nr_zlec)
            <div class="row">
                <div class="col-xl-7">
                    <b-block full>
                        <template slot="content">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-vcenter font-size-sm">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="font-w700" style="width:1%">Lp.</th>
                                            <th class="font-w700">Nr zlecenia</th>
                                            <th class="font-w700">Przyjęcie</th>
                                            <th class="font-w700">Status</th>
                                            <th class="font-w700" nowrap>Ostatnia data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($zlecenia as $key => $zlecenie)
                                            <tr>
                                                <th>{{ $key + 1 }}</th>
                                                {!! $zlecenie->table_cell_nr_html !!}
                                                <td nowrap>{{ $zlecenie->data_przyjecia_formatted }}</td>
                                                {!! $zlecenie->table_cell_status_html !!}
                                                <td nowrap>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </template>
                    </b-block>
                </div>
            </div>
        @endif
    </div>
@endsection
