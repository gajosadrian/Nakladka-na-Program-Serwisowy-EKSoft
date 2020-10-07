@php
    $is_mobile = auth()->user()->is_mobile;
@endphp

@extends('global.app', [ 'window' => $is_mobile ])

@section('content')
    <div class="{{ $is_mobile ? 'bg-primary' : 'bg-body-light' }} d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="{{ $is_mobile ? 'text-white' : '' }} flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Szykowanie części</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" theme="{{ $is_mobile ? 'bg-primary' : '' }}" full>
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

        <b-row>
            @foreach ($terminy as $termin)
                @foreach ($termin->zlecenie->kosztorys_pozycje as $pozycja)
                    @continue( ! $pozycja->is_towar or $pozycja->is_zamontowane or $pozycja->is_rozpisane )

                    @php
                        $is_niezamontowane_gotowe = (bool) ($pozycja->is_niezamontowane and $pozycja->naszykowana_czesc and $pozycja->naszykowana_czesc->sprawdzone_at and $pozycja->naszykowana_czesc->sprawdzone_at->gte($pozycja->naszykowana_czesc->zlecenie_data));
                        $is_naszykowane = (bool) (($pozycja->naszykowana_czesc and $pozycja->naszykowana_czesc->zlecenie_data->gte($date)) ?: $pozycja->is_ekspertyza);

                        $color = null;
                        if ($pozycja->is_ekspertyza or $pozycja->is_zamowione) {
                            $color = 'warning';
                        // } elseif ($pozycja->is_niezamontowane) {
                        //     $color = 'danger';
                        } elseif ($is_naszykowane) {
                            $color = 'success';
                        }
                    @endphp

                    <b-col lg="4">
                        <b-block class="{{ $is_mobile ? 'mb-2' : '' }}">
                            <template slot="content">
                                <div>
                                    {{-- <div>zlecenie_id: {{ $termin->zlecenie_id }}, towar_id: {{ $pozycja->towar_id }}, pozycja_id: {{ $pozycja->id }}, opis: ({{ $pozycja->opis_raw }})</div> --}}
                                    <div onclick="{{ $termin->zlecenie->popup_link }}" style="cursor:pointer;">
                                        {{ $termin->zlecenie->nr }},
                                        <span class="font-w600">{{ $termin->zlecenie->klient->nazwa }}</span>
                                    </div>
                                    @if ($pozycja->naszykowana_czesc)
                                        <div>
                                            Naszykował: <span class="font-w600 text-info">{{ $pozycja->naszykowana_czesc->user->name }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div id="ribbon_{{ $pozycja->id }}" class="ribbon ribbon-{{ $color ? $color : 'danger' }}">
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
                                <div id="label_{{ $pozycja->id }}" class="bg-{{ $color ? $color : 'danger' }} text-white p-1 mb-1">
                                    <div class="clearfix">
                                        <div class="float-left font-w600">
                                            @if ($pozycja->is_czesc_symbol)
                                                {{ $pozycja->opis_fixed }}
                                            @else
                                                <span class="truncate">{{ $pozycja->nazwa }}</span>
                                                {{-- {{ str_limit($pozycja->nazwa, 30) }} --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row gutters-tiny">
                                    <div class="col-4">
                                        @if ( ! $pozycja->is_ekspertyza and ! $pozycja->is_zamowione and (! $pozycja->is_niezamontowane or $is_niezamontowane_gotowe)) {{-- and ! $pozycja->is_niezamontowane --}}
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input id="ilosc_{{ $pozycja->id }}" class="form-control {{ $is_naszykowane ? 'form-control-alt is-valid' : '' }}" type="number" value="{{ $pozycja->naszykowana_czesc ? $pozycja->naszykowana_czesc->ilosc : $pozycja->ilosc }}" onclick="select()">
                                                    <div class="input-group-append">
                                                        <button id="button_{{ $pozycja->id }}" class="btn btn-{{ $is_naszykowane ? '' : 'outline-' }}success" onclick="naszykujCzesc(@json($pozycja->id), Number($('#ilosc_{{ $pozycja->id }}').val()))">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                        {{-- <button class="btn btn-danger" onclick="zamontujCzesc(@json($pozycja->id), Number($('#ilosc_{{ $pozycja->id }}').val()))">
                                                            <i class="fa fa-exclamation-triangle"></i>
                                                        </button> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif ($pozycja->is_niezamontowane and ! $is_niezamontowane_gotowe)
                                            Usuń z opisu "niezałożone" i ustaw ilość
                                        @elseif ($pozycja->naszykowana_czesc and $pozycja->naszykowana_czesc->is_rozliczone)
                                            Pozycja powinna być rozliczona
                                        @endif
                                    </div>
                                    <div class="col-8 text-right">
                                        <div>
                                            <span class="mr-2">{{ $pozycja->symbol_dostawcy }}</span>
                                            <span class="font-w600 bg-info text-white px-1">{{ $pozycja->symbol }}</span>
                                        </div>
                                        <div class="font-w600 text-success">{{ $pozycja->polka }}</div>
                                    </div>
                                </div>
                            </template>
                        </b-block>
                    </b-col>
                @endforeach
            @endforeach
        </b-row>
    </div>
@endsection

@section('js_after')<script>

let technik_id = @json(@$technik->id ?? 0);
let date_string = @json($date_string);

function naszykujCzesc(kosztorys_pozycja_id, ilosc) {
    const $ribbon = $('#ribbon_' + kosztorys_pozycja_id)
    const $label = $('#label_' + kosztorys_pozycja_id)
    const $ilosc = $('#ilosc_' + kosztorys_pozycja_id)
    const $button = $('#button_' + kosztorys_pozycja_id)

    axios.post( route('czesci.updateNaszykuj', {
        kosztorys_pozycja: kosztorys_pozycja_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
        technik_id,
        ilosc,
    }).then((response) => {
        // location.reload();
        $ribbon.removeClass('ribbon-success ribbon-danger ribbon-warning')
        $label.removeClass('bg-success bg-danger bg-warning')
        $ilosc.removeClass('form-control-alt is-valid')
        $button.removeClass('btn-outline-success btn-success')

        if (ilosc > 0) {
            $ribbon.addClass('ribbon-success')
            $label.addClass('bg-success')
            $ilosc.addClass('form-control-alt is-valid')
            $button.addClass('btn-success')
        } else {
            $ribbon.addClass('ribbon-danger')
            $label.addClass('bg-danger')
            $button.addClass('btn-outline-success')
        }
    });
}

// function zamontujCzesc(kosztorys_pozycja_id, ilosc) {
//     axios.post( route('czesci.updateZamontuj', {
//         kosztorys_pozycja: kosztorys_pozycja_id,
//     }), {
//         _token: @json(csrf_token()),
//         _method: 'patch',
//         technik_id,
//         ilosc,
//     }).then((response) => {
//         location.reload();
//     });
// }

function updateUrl(self, type) {
    let value = $(self).val();

    window.location.replace(route('czesci.indexSzykowanie', {
        technik_id: (type == 'technik_id') && value || technik_id,
        date_string: (type == 'date_string') && value || date_string,
    }));
}

</script>@endsection
