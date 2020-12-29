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
        <b-block title="Parametry" theme="{{ $is_mobile ? 'bg-primary' : '' }}" {{ $is_mobile ? 'full' : '' }}>
            <template slot="content">
                @if ($is_mobile)
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
                @else
                    <div class="mb-3">
                        @foreach ($technicy as $_technik)
                            <b-button variant="outline-primary" class="{{ ($_technik->id == @$technik->id) ? 'active' : '' }}" onclick="updateUrl({{ $_technik->id }}, 'technik_id')">
                                {{ $_technik->nazwa }}
                            </b-button>
                        @endforeach
                    </div>
                @endif
            </template>
        </b-block>

        <b-row>
            @foreach ($naszykowane_czesci as $naszykowana_czesc)
                @php
                    $pozycja = $naszykowana_czesc->kosztorys_pozycja;
                    $zlecenie = $naszykowana_czesc->zlecenie;
                    $towar = $naszykowana_czesc->towar;

                    // if ( ! $pozycja) continue;
                    if ($zlecenie->is_odplatne and $naszykowana_czesc->ilosc_do_zwrotu == 0) continue;
                    if (! $towar) continue;
                @endphp

                @if ( ! isset($separator) and ! $naszykowana_czesc->is_zlecenie_data_past )
                    @php
                        $separator = true;
                    @endphp
                    <b-col cols="12" style="margin-top: 300px;">
                        <h2 class="content-heading border-black-op">
                            <i class="si si-wrench mr-1"></i>
                            Części naszykowane na kolejne dni
                        </h2>
                    </b-col>
                @endif

                <b-col lg="6">
                    <b-block id="block_{{ $naszykowana_czesc->id }}" class="border border-danger border-3 {{ $is_mobile ? 'mb-2' : '' }}" full>
                        <template slot="content">
                            <div class="clearfix {{ $is_mobile ? '' : 'push' }}">
                                <div class="float-left">
                                    {{-- {{ $zlecenie->nr }}, --}}
                                    <a href="javascript:void(0)" class="font-w600" onclick="{{ $naszykowana_czesc->zlecenie->popup_link }}">
                                        {{ $zlecenie->klient->nazwa }}
                                    </a>
                                    {{ $naszykowana_czesc->zlecenie_data_formatted }}
                                    <span id="info_{{ $naszykowana_czesc->id }}" class="ml-2">
                                        @if ($naszykowana_czesc->user->technik_id)
                                            <span class="d-none d-sm-inline bg-secondary text-white font-w600 px-1">Część nie była naszykowana</span>
                                        @elseif (! $naszykowana_czesc->technik_updated_at)
                                            <span class="d-block d-sm-inline bg-danger text-white font-w600 px-1">Technik nie odznaczył części</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="float-right">
                                    <div class="d-inline-block px-3 py-1 rounded-lg font-size-sm font-w600 text-info bg-info-lighter">
                                        {{ $zlecenie->zleceniodawca }}
                                    </div>
                                </div>
                            </div>
                            <b-row>
                                <b-col lg="6">
                                    @if ($towar->is_zdjecie)
                                        <div>
                                            <img src="{{ $towar->zdjecie_url }}" alt="zdjęcie" class="img-fluid">
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
                                                @if ($pozycja and $pozycja->is_czesc_symbol)
                                                    {{ $pozycja->opis_fixed }}
                                                @else
                                                    <span class="truncate">{{ $towar->nazwa }}</span>
                                                    {{-- {{ str_limit($towar->nazwa, 30) }} --}}
                                                @endif
                                            </span>
                                            {{-- <small class="text-secondary">({{ $pozycja->state_formatted }})</small> --}}
                                        </div>
                                        <div>
                                            <span class="font-w600 bg-info text-white px-1">{{ $towar->symbol }}</span>
                                            <span class="ml-2">{{ $towar->symbol_dostawcy }}</span>
                                            <span class="ml-2 font-w700 text-success">{{ $towar->polka }}</span>
                                        </div>
                                        <div>
                                            Naszykował: <span class="font-w600 text-info">{{ $naszykowana_czesc->user->name }}</span>
                                        </div>
                                    </div>

                                    @if ($naszykowana_czesc->ilosc_do_zwrotu > 0 or $zlecenie->is_odplatne)
                                        <div>
                                            Do zwrotu nowe:
                                            <span class="font-w600 bg-danger text-white px-1">{{ $naszykowana_czesc->ilosc_do_zwrotu }}</span>
                                            <small class="text-secondary">({{ $naszykowana_czesc->ilosc }})</small>
                                        </div>
                                    @endif
                                    @if (! $zlecenie->is_odplatne and $naszykowana_czesc->ilosc_zamontowane > 0)
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
                                            <span class="font-w600 text-success">{{ $naszykowana_czesc->ilosc_rozpisane }}</span>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <b-button id="sprawdz_{{ $naszykowana_czesc->id }}" variant="outline-success" size="sm" onclick="sprawdzCzesc(@json($naszykowana_czesc->id))">
                                            <i id="icon" class="fa fa-check"></i>
                                            <span id="text">Odebrano</span>
                                        </b-button>
                                    </div>
                                    @if (! $naszykowana_czesc->technik_updated_at)
                                        <hr>
                                        @if (@$pozycja->id)
                                            <div id="zamontowano_buttons_{{ $naszykowana_czesc->id }}">
                                                @for ($i = 1; $i <= $naszykowana_czesc->ilosc; $i++)
                                                    <b-button id="zamontowano_{{ $naszykowana_czesc->id }}" variant="outline-danger" size="sm" onclick="zamontujCzesc(@json(@$pozycja->id), 'zamontowane', @json($i), @json($naszykowana_czesc->id))">
                                                        Zamontowano: {{ $i }}
                                                    </b-button>
                                                @endfor
                                            </div>
                                        @else
                                            Pozycja o ID <code>${{ $naszykowana_czesc->key }}$</code> usunięta z kosztorysu
                                        @endif
                                    @endif
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
    const $block = $('#block_' + naszykowana_czesc_id)
    const $sprawdz = $('#sprawdz_' + naszykowana_czesc_id)
    const $sprawdzText = $sprawdz.find('> #text')
    const $info = $('#info_' + naszykowana_czesc_id)

    $sprawdz.addClass('btn-success').prop('disabled', true)

    axios.post( route('czesci.updateSprawdz', {
        naszykowana_czesc: naszykowana_czesc_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
    }).then((response) => {
        // location.reload()
        $block.removeClass('border-danger')
        $sprawdz.removeClass('btn-outline-success')

        $block.addClass('border-success')
        $sprawdzText.text('Sprawdzone')
        $info.remove()
    });
}

function zamontujCzesc(pozycja_id, type, ilosc, naszykowana_czesc_id) {
    const $sprawdz = $('#sprawdz_' + naszykowana_czesc_id)
    const $sprawdzIcon = $sprawdz.find('> #icon')
    const $sprawdzText = $sprawdz.find('> #text')
    const $zamontowanoButtons = $(`#zamontowano_buttons_${naszykowana_czesc_id} > button`)

    $sprawdz.prop('disabled', true)
    $sprawdzIcon.remove()
    $sprawdzText.html('<i class="fa fa-spinner fa-pulse"></i> Czekaj...')
    $zamontowanoButtons.each(function () {
        $(this).prop('disabled', true)
    })

    axios.post( route('czesci.updateZamontuj', {
        kosztorys_pozycja: pozycja_id,
    }), {
        _token: @json(csrf_token()),
        _method: 'patch',
        technik_id,
        type,
        ilosc,
    })
        .then(response => {
            sprawdzCzesc(naszykowana_czesc_id)
        })
        .catch(error => {
            console.log(error)
        })
}

function updateUrl(self, type) {
    let value;
    if (Number.isInteger(self)) {
        value = self;
    } else {
        value = $(self).val()
    }

    window.location.replace(route('czesci.indexOdbior', {
        technik_id: (type == 'technik_id') && value || technik_id,
    }));
}

</script>@endsection
