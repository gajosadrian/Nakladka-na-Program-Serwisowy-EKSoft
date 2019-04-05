@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zlecenia dla technika</h1>
            </div>
       </div>
    </div>

    <div class="content">
        <b-block title="Wybierz technika" full class="d-print-none">
            <template slot="content">
                @foreach ($technicy as $_technik)
                    <b-link href="{{ route('zlecenia.dla-technika', [ 'technik_id' => $_technik->id, 'timestamp' => $timestamp ]) }}" class="btn btn-outline-primary {{ ($_technik->id == $technik_id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                @endforeach

                <input type="text" class="js-datepicker form-control" value="{{ $date_string }}"
                    onclick="updateUrl()">
            </template>
        </b-block>

        @if ($technik)
            <b-block title="Zlecenia" noprint>
                <template slot="options">
                    <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                        <i class="fa fa-print"></i> Drukuj
                    </b-button>
                </template>
                <template slot="content">
                    <p>{{ $date_formatted }}</p>
                    @foreach ($zlecenia as $zlecenie)
                        <div class="mb-5">
                            {{ $zlecenie->terminarz->godzina_rozpoczecia }} - {{ $zlecenie->terminarz->przeznaczony_czas_formatted }}<br>
                            Zlecenie nr {{ $zlecenie->nr }} - {{ $zlecenie->nr_obcy }}<br>
                            {{ $zlecenie->klient->symbol }} {{ $zlecenie->klient->nazwa }}<br>
                        </div>
                    @endforeach
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

function updateUrl() {
    window.location.replace(route('zlecenia.dla-technika'))
}

</script>@endsection
