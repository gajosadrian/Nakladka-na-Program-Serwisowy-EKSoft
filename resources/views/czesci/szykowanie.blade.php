@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Szykowanie części</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
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
            @foreach ($termin->zlecenie->kosztorys_pozycje as $towar)
                @continue(!$towar->is_towar)

                <b-block class="mb-2">
                    <template slot="content">
                        <div>
                            <div>{{ $termin->zlecenie->nr }}, <span class="font-w600">{{ $termin->zlecenie->klient->nazwa }}</span></div>
                        </div>
                        <div class="ribbon ribbon-{{ false ? 'success' : 'danger' }}">
                            <div class="ribbon-box">{{ $towar->state_formatted }}</div>
                            @if ($towar->is_zdjecie)
                                <div>
                                    <img src="{{ $towar->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                                </div>
                            @else
                                <div class="py-5 text-center border">
                                    <span class="font-w600"><i class="fa fa-camera fa-2x"></i> Brak zdjęcia</span>
                                </div>
                            @endif
                        </div>
                        <div class="bg-danger text-white p-1 mb-1">
                            <div class="clearfix">
                                <div class="float-left font-w700">
                                    @if ($towar->is_czesc_symbol)
                                        {{ $towar->opis_fixed }}
                                    @else
                                        {{ str_limit($towar->nazwa, 30) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row gutters-tiny">
                            <div class="col-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control {{ false ? 'form-control-alt is-valid' : '' }}" type="number" value="{{ $towar->ilosc }}" onclick="select()">
                                        <div class="input-group-append">
                                            <button class="btn btn-{{ false ? '' : 'outline-' }}success"><i class="fa fa-check"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-8 text-right">
                                <div>
                                    <span class="mr-2">{{ $towar->symbol_dostawcy }}</span>
                                    <span class="font-w600 bg-info text-white px-1">{{ $towar->symbol }}</span>
                                </div>
                                <div class="font-w700 text-success">{{ $towar->polka }}</div>
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

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('czesci.indexSzykowanie', {
        technik_id: (type == 'technik_id') && value || technik_id,
        date_string: (type == 'date_string') && value || date_string,
    }));
}

</script>@endsection
