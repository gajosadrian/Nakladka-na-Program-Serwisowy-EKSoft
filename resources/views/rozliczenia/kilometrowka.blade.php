@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Kilometrówka</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full class="d-print-none">
            <template slot="content">
                @if (! $is_technik)
                    <div class="mb-3">
                        @foreach ($technicy as $_technik)
                            <b-link href="{{ route('zlecenia.kilometrowka', [ 'technik_id' => $_technik->id, 'month_id' => $month_id ]) }}" class="btn btn-outline-primary {{ ($_technik->id == $technik_id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                        @endforeach
                    </div>
                @endif

                <b-row>
                    @if ($technik)
                        <b-col cols="2">
                            <select class="form-control" onchange="updateUrl(this)">
                                <option value="0">Poprzedni miesiąc</option>
                                @foreach ($months as $month)
                                    <option value="{{ $month->id }}" {{ ($month->id == $month_id) ? 'selected' : '' }}>{{ $month->name }}</option>
                                @endforeach
                            </select>
                        </b-col>
                        <b-col cols="2">
                            <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                                <i class="fa fa-print"></i> Drukuj
                            </b-button>
                        </b-cols>
                    @endif
                </b-row>
            </template>
        </b-block>

        @if ($technik)
            <b-block title="Zlecenia" noprint>
                <template slot="content">
                    <ol>
                        @foreach ($grouped_terminy as $date_string => $grouped_termin)
                            <li class="text-danger font-w600">{{ $date_string }}</li>
                            @foreach ($grouped_termin as $termin)
                                @php
                                    $zlecenie = $termin->zlecenie;
                                @endphp
                                @if ($zlecenie->id)
                                    <li><a href="javascript:void(0)" onclick="{{ $zlecenie->popup_link }}">{{ $zlecenie->nr }}</a>, {{ $termin->samochod['value'][0] }}</li>
                                @elseif ($termin->temat)
                                    <li>{{ $termin->temat }}</li>
                                @endif
                            @endforeach
                        @endforeach
                    </ol>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('zlecenia.kilometrowka', {
        technik_id: @json($technik_id),
        month_id: value,
    }));
}

</script>@endsection
