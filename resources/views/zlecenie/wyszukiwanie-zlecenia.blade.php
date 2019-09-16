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
                            <input name="nr_zlec" type="text" class="form-control" value="{{ $zlecenie ? $zlecenie->nr : '' }}">
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

        @if ($zlecenie_id)
            <b-block full>
                <template slot="content">
                    <a href="javascript:void(0)" class="btn btn-primary" onclick="{{ $zlecenie->popup_link }}">Otwórz</a>
                </template>
            </b-block>
        @endif
    </div>
@endsection
