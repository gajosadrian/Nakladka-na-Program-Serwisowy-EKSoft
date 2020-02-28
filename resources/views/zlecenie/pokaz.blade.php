@extends('global.app', [ 'window' => true ])
{{-- @inject('App\Models\Zlecenie\Status', 'App\Models\Zlecenie\Status') --}}

@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="content">
		{{-- <b-row>
			@if ($zlecenie->status->id == App\Models\Zlecenie\Status::ZAMOWIONO_CZESC_ID)
                <b-col cols="12">
                    <div class="alert alert-warning d-flex align-items-center">
                        <div class="flex-00-auto">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-fill ml-3">
                            <p class="mb-0">
								Jeżeli część dotarła do serwisu, należy w kosztorysie przy zamówionych częściach zmienić opis na <strong>„<u>odłożono</u>”</strong> i odłożyć opisaną część na magazyn<br>
								oraz zmienić status na <strong>„<u>{{ App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID) }}</u>”</strong> i znaleźć zlecenie na półce, jeżeli wszystkie części dotarły.
							</p>
                        </div>
                    </div>
                </b-col>
			@endif
		</b-row> --}}
        <b-row class="row-deck">
            <b-col lg="5">
                <b-block title="Kontrahent" theme="bg-primary-light">
                    <template slot="content">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-right" style="width:1%">Nazwa:</th>
                                <td>{{ $zlecenie->klient->nazwa }} <span class="small text-muted">({{ $zlecenie->klient->symbol }})</span></td>
                            </tr>
                            <tr>
                                <th class="text-right">Ulica:</th>
                                <td>{{ $zlecenie->klient->adres }}</td>
                            </tr>
                            <tr>
                                <th class="text-right">Miasto:</th>
                                <td>{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}</td>
                            </tr>
                            <tr>
                                <th class="text-right">Telefony:</th>
                                <td>{{ $zlecenie->klient->telefony_formatted }}</td>
                            </tr>
                            <tr>
                                <th class="text-right" nowrap>Akc. kosztów:</th>
                                <td nowrap>
                                    @if ($zlecenie->is_akc_kosztow)
                                        <span class="text-success font-w600">{{ $zlecenie->data_akc_kosztow }}</span>
                                    @else
                                        <span class="text-muted"><i>Brak informacji</i></span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-right">Rozliczenie:</th>
                                <td class="text-success font-w600">{{ $zlecenie->is_rozliczenie ? $zlecenie->rozliczenie->rozliczenie->nr : '-' }}</td>
                            </tr>
                        </table>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="7">
                <b-block title="Dane zlecenia" theme="bg-primary-light">
                    <template slot="content">
                        <b-row>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%">Numer:</th>
                                        <td>
                                            {{ $zlecenie->nr }}
                                            <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{{ $zlecenie->nr }}'">
                                                <i class="far fa-copy"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Nr&nbsp;obcy:</th>
                                        <td>{{ $zlecenie->nr_obcy ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Rodzaj:</th>
                                        <td><i class="{{ $zlecenie->znacznik->icon }}"></i> {{ $zlecenie->znacznik_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Źródło:</th>
                                        <td><i class="{{ $zlecenie->zrodlo->icon }}"></i> {{ $zlecenie->zrodlo->nazwa }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        @if ($user->is_technik)
                                            <td class="align-middle {{ $zlecenie->status->color ? 'table-' . $zlecenie->status->color : '' }}">
                                                <i class="{{ $zlecenie->status->icon }} {{ $zlecenie->status->color ? 'text-' . $zlecenie->status->color : '' }} mx-2"></i>
        										{{ $zlecenie->status->nazwa }}
                                            </td>
                                        @else
                                            <td>
                                                <div class="row gutters-tiny">
                                                    <div class="col-10">
                                                        <select id="status_select" class="form-control form-control-sm font-w700 {{ $zlecenie->status->color ? 'bg-'.$zlecenie->status->color.'-lighter' : '' }}">
                                                            <option value="{{ $zlecenie->status->id }}" selected disabled>{{ $zlecenie->status->nazwa }}</option>
                                                            @foreach ($statusy_aktywne as $status)
                                                                <option value="{{ $status->id }}" {{ ($status->id == $zlecenie->status->id) ? 'selected' : '' }}>{{ $status->nazwa }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <b-button variant="primary" size="sm" onclick="changeStatus(Number($('#status_select').val()))">Ok</b-button>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Przyjmujący:</th>
                                        <td>{{ $zlecenie->przyjmujacy->nazwa }}</td>
                                    </tr>
                                </table>
                            </b-col>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%">Czas&nbsp;trwania:</th>
                                        <td>{{ $zlecenie->czas_trwania_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Przyjęcie:</th>
                                        <td>{{ $zlecenie->data_przyjecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Zakończenie:</th>
                                        <td>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;od:</th>
                                        <td>{{ $zlecenie->terminarz->data_rozpoczecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Termin&nbsp;do:</th>
                                        <td>{{ $zlecenie->terminarz->data_zakonczenia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Technik:</th>
                                        <td>{{ $zlecenie->technik->nazwa }}</td>
                                    </tr>
                                </table>
                            </b-col>
                        </b-row>
                    </template>
                </b-block>
            </b-col>
        </b-row>
        <b-row class="row-deck">
            <b-col lg="3">
                <b-block title="Urządzenie" theme="bg-primary-light">
                    <template slot="content">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:1%">Nazwa:</th>
                                <td>{{ $zlecenie->urzadzenie->nazwa }}</td>
                            </tr>
                            <tr>
                                <th>Producent:</th>
                                <td>{{ $zlecenie->urzadzenie->producent }}</td>
                            </tr>
                            <tr>
                                <th>Model:</th>
                                <td>{!! $zlecenie->urzadzenie->model ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Kod&nbsp;wyrobu:</th>
                                <td>{!! $zlecenie->urzadzenie->kod_wyrobu ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Nr seryjny:</th>
                                <td>{!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="font-w600 text-danger">uzupełnić</span>' !!}</td>
                            </tr>
                        </table>
                    </template>
                </b-block>
            </b-col>
            <b-col lg="9">
                <div class="block block-rounded shadow-sm">
                    <ul class="nav nav-tabs nav-tabs-alt align-items-center js-tabs bg-primary-light" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#kosztorys" class="nav-link" style="color: rgba(255, 255, 255, 0.9)">Kosztorys</a>
                        </li>
                        <li class="nav-item">
                            <a href="#opis" class="nav-link active show" style="color: rgba(255, 255, 255, 0.9)">Opis</a>
                        </li>
                        <li class="nav-item">
                            <a href="#statusy" class="nav-link" style="color: rgba(255, 255, 255, 0.9)">Statusy</a>
                        </li>
                        <li class="nav-item">
                            {{-- <button type="button" class="nav-link" style="color: rgba(255, 255, 255, 0.9)" onclick="{{ $zlecenie->popup_zdjecia_link }}">Zdjęcia</button> --}}
                            <a href="#zdjecia" class="nav-link" style="color: rgba(255, 255, 255, 0.9)">Zdjęcia</a>
                        </li>
                        <li class="nav-item ml-auto">
                            <b-button-group size="sm" class="mr-2">
                                @if ($user->is_technik)
                                    @if ($zlecenie->status->id == App\Models\Zlecenie\Status::NA_WARSZTACIE_ID)
                                        <zlecenie-change-status
                                            zlecenie_id=@json($zlecenie->id)
                                            status_id=@json(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID)
                                            remove_termin="0"
                                            name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                            icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                            color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                        ></zlecenie-change-status>
                                        @if ($zlecenie->is_gwarancja)
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID)
                                                remove_termin="0"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                            ></zlecenie-change-status>
                                        @else
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DO_WYCENY_ID)
                                                remove_termin="0"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                            ></zlecenie-change-status>
                                        @endif
                                        {{-- @if ($zlecenie->is_odplatne)
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID)
                                                remove_termin="0"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                            ></zlecenie-change-status>
                                        @endif --}}
                                        @if ($zlecenie->was_warsztat)
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID)
                                                remove_termin="0"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID))
                                            ></zlecenie-change-status>
                                        @else
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID)
                                                remove_termin="1"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                            ></zlecenie-change-status>
                                        @endif
                                    @endif
                                @elseif ( ! $user->is_technik)
                                    @if (count($zlecenie->errors) > 0)
                                        <b-button onclick="zatwierdzBlad()" size="sm" variant="light"><i class="fa fa-exclamation-triangle text-danger" class="ml-1"></i> Usuń błąd</b-button>
                                    @endif

                                    {{-- @if ($zlecenie->status->id == App\Models\Zlecenie\Status::ZAMOWIONO_CZESC_ID)
                                        <zlecenie-change-status class="ml-1"
                                            zlecenie_id=@json($zlecenie->id)
                                            status_id=@json(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID)
                                            remove_termin="1"
                                            name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                            icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
    										color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))></zlecenie-change-status>
                                    @endif --}}
                                @endif
                            </b-button-group>
                        </li>
                    </ul>
                    <div class="block-content tab-content overflow-hidden block-content-full">
                        <div class="tab-pane fade" id="kosztorys" role="tabpanel">
                            <table class="table table-sm table-striped table-vcenter font-size-sm">
                                <thead>
                                    <tr>
                                        <th class="font-w700" nowrap>Symbol dost.</th>
                                        <th class="font-w700" nowrap>Symbol</th>
                                        <th class="font-w700" nowrap>Nazwa</th>
                                        <th class="font-w700" nowrap>Opis</th>
                                        <th class="font-w700 text-right" nowrap>Cena brutto</th>
                                        <th class="font-w700 text-center" nowrap>Ilość</th>
                                        <th class="font-w700 text-right" nowrap>Wartość netto</th>
                                        <th class="font-w700 text-right" nowrap>Wartość brutto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $wartosc_netto = 0.00;
                                        $wartosc_brutto = 0.00;
                                    @endphp
                                    @foreach ($zlecenie->kosztorys_pozycje as $pozycja)
                                        @php
                                            $wartosc_netto += $pozycja->wartosc;
                                            $wartosc_brutto += $pozycja->wartosc_brutto;
                                        @endphp
                                        <tr>
                                            <td nowrap>{{ $pozycja->symbol_dostawcy }}</td>
                                            <td nowrap>{{ $pozycja->symbol }}</td>
                                            <td nowrap>
                                                @if ($pozycja->is_towar and $pozycja->is_zamowione)
                                                    <i class="fa fa-shopping-cart text-danger"></i>
                                                @endif
                                                {{ str_limit($pozycja->nazwa, 50) }}
                                            </td>
                                            <td class="small" nowrap>
                                                @if ( ! $user->is_technik)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control form-control-sm" value="{{ $pozycja->opis_fixed }}" onkeyup="changeOpis(@json($pozycja->id), $(this).val())" onclick="this.select()">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i id="pozycja_status_success{{ $pozycja->id }}" class="fa fa-check text-success"></i>
                                                                <i id="pozycja_status_sending{{ $pozycja->id }}" class="d-none fa fa-circle-notch fa-spin text-secondary"></i>
                                                                <i id="pozycja_status_error{{ $pozycja->id }}" class="d-none fa fa-times text-danger"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @else
                                                    {{ $pozycja->opis_fixed }}
                                                @endif
                                            </td>
                                            <td class="text-right" nowrap>{{ $pozycja->cena_brutto_formatted }}</td>
                                            <td class="text-center {{ $pozycja->ilosc > 1 ? 'font-w600 text-danger' : '' }}" nowrap>{{ $pozycja->ilosc }}</td>
                                            <td class="text-right" nowrap>{{ $pozycja->wartosc_formatted }}</td>
                                            <td class="text-right" nowrap>{{ $pozycja->wartosc_brutto_formatted }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">{{ number_format($wartosc_netto, 2, '.', ' ') }} zł</th>
                                        <th class="text-right">{{ number_format($wartosc_brutto, 2, '.', ' ') }} zł</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane fade active show" id="opis" role="tabpanel">
                            @if ($zlecenie->errors)
                                <div class="font-w600 text-danger">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    {{ implode(', ', $zlecenie->errors) }}
                                </div>
                            @endif
                            <zlecenie-opis zlecenie_id=@json($zlecenie->id) />
                        </div>
                        <div class="tab-pane fade" id="statusy" role="tabpanel">
                            <b-row>
                                <b-col cols="12" xl="8">
                                    <table class="table table-sm table-striped table-vcenter font-size-sm">
                                        <thead>
                                            <tr>
                                                <th class="font-w700" nowrap>Status</th>
                                                <th class="font-w700" nowrap>Data</th>
                                                <th class="font-w700" nowrap>Użytkownik</th>
                                                @role('super-admin')
                                                    <th class="font-w700"></th>
                                                @endrole
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($zlecenie->statusy as $status_pozycja)
                                                @php
                                                    $status = $status_pozycja->status;
                                                @endphp
                                                <tr>
                                                    <td nowrap class="align-middle {{ $status->color ? 'table-' . $status->color : '' }}">
                                                        <i class="{{ $status->icon }} {{ $status->color ? 'text-' . $status->color : '' }} mx-2"></i>
                                                        {{ $status->nazwa }}
                                                    </td>
                                                    <td nowrap>{{ $status_pozycja->data }}</td>
                                                    <td nowrap>{{ $status_pozycja->pracownik->nazwa }}</td>
                                                    @role('super-admin')
                                                        <td nowrap>
                                                            <button onclick="removeStatus(this,{{ $status_pozycja->id }})" type="button" class="btn btn-sm btn-rounded btn-danger">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </td>
                                                    @endrole
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </b-col>
                            </b-row>
                        </div>
                        <div class="tab-pane fade" id="zdjecia" role="tabpanel">
                            @include('zlecenie-zdjecie.component.index', compact('zlecenie'))
                        </div>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>
@endsection

@section('js_after')<script>

$(document).keydown(function (e) {
	if (e.keyCode == 27) {
		window.close();
	}
});

var last_status_id = @json($zlecenie->status_id);
function changeStatus(status_id) {
    if (last_status_id == status_id) {
        swal({
            position: 'center',
            type: 'error',
            title: 'Już ustawiono ten status',
            showConfirmButton: false,
            timer: 1500,
        });
        return;
    }

    last_status_id = status_id;

    let remove_termin = 0;
    if (status_id == 14 || status_id == 13) {
        remove_termin = 1;
    }

    axios.post( route('zlecenia.api.change_status', {
        id: @json($zlecenie->id),
        status_id,
        remove_termin,
    })).then((response) => {
        swal({
            position: 'center',
            type: 'success',
            title: 'Status zmieniony',
            showConfirmButton: false,
            timer: 1500,
        });
    });
}

var timers = [];
function changeOpis(pozycja_id, opis) {
    pozycjaStatus(pozycja_id, 'sending');

    clearTimeout(timers[pozycja_id]);
    timers[pozycja_id] = setTimeout(function () {
        axios.post( route('zlecenia.api.changeKosztorysPozycjaOpis', {
            kosztorys_pozycja: pozycja_id,
            opis,
        }), {
            _token: @json(csrf_token()),
        }).then((response) => {
            pozycjaStatus(pozycja_id, 'success');
        }).catch((response) => {
            pozycjaStatus(pozycja_id, 'error');
        });
    }, 1000);
}

function pozycjaStatus(pozycja_id, state) {
    let $success = $('#pozycja_status_success' + pozycja_id);
    let $sending = $('#pozycja_status_sending' + pozycja_id);
    let $error = $('#pozycja_status_error' + pozycja_id);

    $sending.removeClass('d-none').addClass('d-none');
    $success.removeClass('d-none').addClass('d-none');
    $error.removeClass('d-none').addClass('d-none');

    switch (state) {
        case 'sending':
            $sending.removeClass('d-none');
            break;
        case 'success':
            $success.removeClass('d-none');
            break;
        case 'error':
            $error.removeClass('d-none');
            break;
    }
}

function zatwierdzBlad() {
    axios.post(route('zlecenia.api.zatwierdz_blad', { id: @json($zlecenie->id) }))
        .then(function (response) {
            window.close();
        });
}

function removeStatus(el, id) {
    let $this = $(el).parent().closest('tr');

    $.post(route('zlecenia.api.removeStatus', {
        status_id: id
    }), {
        _token: '{{ csrf_token() }}',
        _method: 'delete'
    })
    .done(function (data) {
        $this.addClass('table-danger');
    });
}

</script>@append
