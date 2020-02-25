@php
    $is_mobile = auth()->user()->is_mobile;
@endphp

@extends('global.app', [ 'window' => $is_mobile ])

@section('content')
    <div class="{{ $is_mobile ? 'bg-primary' : 'bg-body-light' }} d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="{{ $is_mobile ? 'text-white' : '' }} flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Odbiór części</h1>
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
                </b-row>
            </template>
        </b-block>

        <b-row>
            @foreach ($naszykowane_czesci as $naszykowana_czesc)
                @php
                    $pozycja = $naszykowana_czesc->kosztorys_pozycja;
                    if ( ! $pozycja) continue;
                @endphp
                <b-col lg="6">
                    <b-block class="{{ $is_mobile ? 'mb-2' : '' }}" full>
                        <template slot="content">
                            <div class="{{ $is_mobile ? '' : 'push' }}">
                                {{ $pozycja->zlecenie->nr }},
                                {{-- <span class="font-w600">{{ $pozycja->zlecenie->klient->nazwa }}</span>, --}}
                                <span class="font-w600">{{ $naszykowana_czesc->zlecenie_data_formatted }}</span>
                                <span class="ml-2">
                                    @if ($naszykowana_czesc->user->technik_id)
                                        <span class="d-none d-sm-inline bg-secondary text-white font-w600 px-1">Część nie była naszykowana</span>
                                    @elseif ( ! $naszykowana_czesc->technik_updated_at)
                                        <span class="d-none d-sm-inline bg-danger text-white font-w600 px-1">Technik nie odznaczył części</span>
                                    @endif
                                </span>
                            </div>
                            <b-row>
                                <b-col lg="6">
                                    @if ($pozycja->is_zdjecie)
                                        <div>
                                            <img src="{{ $pozycja->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
                                        </div>
                                    @else
                                        <div class="py-5 text-center border">
                                            <span class="font-w600"><i class="fa fa-camera fa-2x"></i> Brak zdjęcia</span>
                                        </div>
                                    @endif
                                </b-col>
                                <b-col lg="6">
                                    <div class="push">
                                        <div>
                                            <span class="font-w700">
                                                @if ($pozycja->is_czesc_symbol)
                                                    {{ $pozycja->opis_fixed }}
                                                @else
                                                    {{ str_limit($pozycja->nazwa, 30) }}
                                                @endif
                                            </span>
                                            {{-- <small class="text-secondary">({{ $pozycja->state_formatted }})</small> --}}
                                        </div>
                                        <div>
                                            <span class="font-w600 bg-info text-white px-1">{{ $pozycja->symbol }}</span>
                                            <span class="ml-2">{{ $pozycja->symbol_dostawcy }}</span>
                                            <span class="ml-2 font-w700 text-success">{{ $pozycja->polka }}</span>
                                        </div>
                                    </div>

                                    @if ($naszykowana_czesc->ilosc_do_zwrotu > 0 or ! $pozycja->zlecenie->is_gwarancja)
                                        <div>
                                            Do zwrotu nowe:
                                            <span class="font-w600 bg-danger text-white px-1">{{ $naszykowana_czesc->ilosc_do_zwrotu }}</span>
                                            <small class="text-secondary">({{ $naszykowana_czesc->ilosc }})</small>
                                        </div>
                                    @endif
                                    @if ($pozycja->zlecenie->is_gwarancja and $naszykowana_czesc->ilosc_zamontowane > 0)
                                        <div>
                                            Do zwrotu zużyte:
                                            <span class="font-w600 bg-warning text-white px-1">{{ $naszykowana_czesc->ilosc_zamontowane }}</span>
                                            <small class="text-secondary">({{ $naszykowana_czesc->ilosc }})</small>
                                        </div>
                                    @endif
                                    @if ($naszykowana_czesc->ilosc_zamontowane > 0 or $naszykowana_czesc->ilosc_rozpisane == 0)
                                        <div>
                                            Zamontowano:
                                            <span class="font-w600 text-success">{{ $naszykowana_czesc->ilosc_zamontowane }}</span>
                                        </div>
                                    @endif
                                    @if ($naszykowana_czesc->ilosc_rozpisane > 0)
                                        <div>
                                            Rozpisano:
                                            <span class="font-w600 text-success">{{ $naszykowana_czesc->ilosc_zamontowane }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <b-button variant="outline-success" size="sm" onclick="sprawdzCzesc(@json($naszykowana_czesc->id))">
                                            <i class="fa fa-check"></i>
                                            Sprawdzone
                                        </b-button>
                                    </div>
                                </b-col>
                            </b-row>
                        </template>
                    </b-block>
                </b-col>
            @endforeach
        </b-row>
    </div>
@endsection

@section('js_after')<script>

let technik_id = @json(@$technik->id ?? 0);

function sprawdzCzesc(naszykowana_czesc_id) {
    axios.post( route('czesci.updateSprawdz', {
        naszykowana_czesc: naszykowana_czesc_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
    }).then((response) => {
        location.reload();
    });
}

function updateUrl(self, type) {
    let value = $(self).val();

    window.location.replace(route('czesci.indexOdbior', {
        technik_id: (type == 'technik_id') && value || technik_id,
    }));
}

</script>@endsection
