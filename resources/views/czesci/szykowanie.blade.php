@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="bg-primary d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="text-white flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Szykowanie części</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" theme="bg-primary" full>
            <template slot="content">
                <b-row>
                    <b-col>
                        <select class="form-control form-control-alt" onchange="updateUrl(this, 'technik_id')">
                            <option value="0">-- Technik --</option>
                            @foreach ($technicy as $_technik)
                                <option value="{{ $_technik->id }}" {{ ($_technik->id == @$technik->id) ? 'selected' : '' }}>
                                    {{ $_technik->nazwa }}
                                </option>
                            @endforeach
                        </select>
                    </b-col>
                    <b-col>
                        <input type="text" class="js-datepicker form-control form-control-alt" value="{{ $date_string }}" onchange="updateUrl(this, 'date_string')">
                    </b-col>
                </b-row>
            </template>
        </b-block>
        @foreach ($terminy as $termin)
            @foreach ($termin->zlecenie->kosztorys_pozycje as $pozycja)
                @continue( !$pozycja->is_towar or $pozycja->is_zamontowane or $pozycja->is_rozpisane )

                <b-block class="mb-2">
                    <template slot="content">
                        <div>
                            <div>zlecenie_id: {{ $termin->zlecenie_id }}, towar_id: {{ $pozycja->towar_id }}, pozycja_id: {{ $pozycja->id }}, opis: ({{ $pozycja->opis }})</div>
                            <div>{{ $termin->zlecenie->nr }}, <span class="font-w600">{{ $termin->zlecenie->klient->nazwa }}</span></div>
                        </div>
                        <div class="ribbon ribbon-{{ $pozycja->naszykowana_czesc ? 'success' : 'danger' }}">
                            <div class="ribbon-box">{{ $pozycja->state_formatted }}</div>
                            @if ($pozycja->is_zdjecie)
                                <div>
                                    <img src="{{ $pozycja->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                                </div>
                            @else
                                <div class="py-5 text-center border">
                                    <span class="font-w600"><i class="fa fa-camera fa-2x"></i> Brak zdjęcia</span>
                                </div>
                            @endif
                        </div>
                        <div class="bg-{{ $pozycja->naszykowana_czesc ? 'success' : 'danger' }} text-white p-1 mb-1">
                            <div class="clearfix">
                                <div class="float-left font-w700">
                                    @if ($pozycja->is_czesc_symbol)
                                        {{ $pozycja->opis_fixed }}
                                    @else
                                        {{ str_limit($pozycja->nazwa, 30) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row gutters-tiny">
                            <div class="col-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        @php
                                            $random = str_random(8);
                                        @endphp
                                        <input id="ilosc_{{ $random }}" class="form-control {{ $pozycja->naszykowana_czesc ? 'form-control-alt is-valid' : '' }}" type="number" value="{{ $pozycja->naszykowana_czesc ? $pozycja->naszykowana_czesc->ilosc : $pozycja->ilosc }}" onclick="select()">
                                        <div class="input-group-append">
                                            <button class="btn btn-{{ $pozycja->naszykowana_czesc ? '' : 'outline-' }}success" onclick="naszykujCzesc(@json($pozycja->id), Number($('#ilosc_{{ $random }}').val()))">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="zamontujCzesc(@json($pozycja->id), @json($pozycja->towar_id), Number($('#ilosc_{{ $random }}').val()))">
                                                <i class="fa fa-exclamation-triangle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8 text-right">
                                <div>
                                    <span class="mr-2">{{ $pozycja->symbol_dostawcy }}</span>
                                    <span class="font-w600 bg-info text-white px-1">{{ $pozycja->symbol }}</span>
                                </div>
                                <div class="font-w700 text-success">{{ $pozycja->polka }}</div>
                            </div>
                        </div>
                    </template>
                </b-block>
            @endforeach
        @endforeach
    </div>
@endsection

@section('js_after')<script>

let technik_id = @json(@$technik->id ?? 0);
let date_string = @json($date_string);

function naszykujCzesc(kosztorys_pozycja_id, ilosc) {
    axios.post( route('czesci.updateNaszykuj', {
        kosztorys_pozycja: kosztorys_pozycja_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
        technik_id,
        ilosc,
    }).then((response) => {
        location.reload();
    });
}

function zamontujCzesc(kosztorys_pozycja_id, towar_id, ilosc) {
    axios.post( route('czesci.updateZamontuj', {
        kosztorys_pozycja: kosztorys_pozycja_id,
        towar_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
        technik_id,
        ilosc,
    }).then((response) => {
        location.reload();
    });
}

function updateUrl(self, type) {
    let value = $(self).val();

    window.location.replace(route('czesci.indexSzykowanie', {
        technik_id: (type == 'technik_id') && value || technik_id,
        date_string: (type == 'date_string') && value || date_string,
    }));
}

</script>@endsection
