@extends('global.app', [ 'window' => true ])
{{-- @inject('App\Models\Zlecenie\Status', 'App\Models\Zlecenie\Status') --}}

@php
    $user = auth()->user();
    $is_up_to_date_termin = ($zlecenie->terminarz->is_data_rozpoczecia and $zlecenie->data_zakonczenia->copy()->startOfDay()->gte(today()));
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
                <b-block title="Kontrahent" theme="bg-primary-light" header-sm>
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
                                    @if ($user->is_technik)
                                        @if ($zlecenie->is_akc_kosztow)
                                            <span class="text-success font-w600">{{ $zlecenie->data_akc_kosztow }}</span>
                                        @else
                                            <span class="text-muted"><i>Brak informacji</i></span>
                                        @endif
                                    @else
                                        <zlecenie-akc-kosztow _token=@json(csrf_token()) :zlecenie_id="{{ $zlecenie->id }}"></zlecenie-akc-kosztow>
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
                <b-block title="Dane zlecenia" theme="bg-primary-light" header-sm>
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
                                        <td>
                                            @if ($user->is_technik)
                                                {{ $zlecenie->nr_obcy ?: '-' }}
                                            @else
                                                <span onclick="jQuery('#nrobcy-modal').modal('show')" style="cursor:pointer;">
                                                    {{ $zlecenie->nr_obcy ?: '-' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Rodzaj:</th>
                                        <td>
                                            @if ($user->is_technik)
                                                <i class="{{ $zlecenie->znacznik->icon }}"></i>
                                                {{ $zlecenie->znacznik_formatted }}
                                            @else
                                                <span onclick="jQuery('#zleceniodawca-modal').modal('show')" style="cursor:pointer;">
                                                    <i class="{{ $zlecenie->znacznik->icon }}"></i>
                                                    {{ $zlecenie->znacznik_formatted }}
                                                </span>
                                            @endif
                                        </td>
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
                                                                <option value="{{ $status->id }}" {{ ($status->id == $zlecenie->status->id) ? 'selected' : '' }}>
                                                                    {{ $status->nazwa }}
                                                                    @if (in_array($status->id, [11, 14, 12, 16, 40, 13, 43, 31]) and $zlecenie->status_id !== $status->id)
                                                                        ✉
                                                                    @endif
                                                                    @if ((in_array($status->id, [13, 29]) or $status->id == 14 and ! $is_up_to_date_termin) and $zlecenie->status_id != $status->id)
                                                                        ✖
                                                                    @endif
                                                                </option>
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
                                </table>
                            </b-col>
                            <b-col lg="6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th style="width:1%;">Przyjmujący:</th>
                                        <td>{{ $zlecenie->przyjmujacy->nazwa }}</td>
                                    </tr>
                                    <tr>
                                        <th>Przyjęcie:</th>
                                        <td>{{ $zlecenie->data_przyjecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th nowrap>Czas trwania:</th>
                                        <td>{{ $zlecenie->czas_trwania_formatted }}</td>
                                    </tr>
                                    {{-- <tr>
                                        <th>Zakończenie:</th>
                                        <td>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                    </tr> --}}
                                    <tr>
                                        <th nowrap>Termin:</th>
                                        <td>{{ $zlecenie->terminarz->data_rozpoczecia_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th nowrap>Przeznaczony czas:</th>
                                        <td>{{ $zlecenie->terminarz->przeznaczony_czas_formatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>Technik:</th>
                                        @if ($user->is_technik)
                                            <td>{{ $zlecenie->technik->nazwa }}</td>
                                        @else
                                            <td>
                                                <div class="row gutters-tiny">
                                                    <div class="col-10">
                                                        <select id="technik_select" class="form-control form-control-sm">
                                                            <option value="0">Brak technika</option>
                                                            @foreach ($technicy as $technik)
                                                                <option value="{{ $technik->id }}" {{ ($technik->id == $zlecenie->technik_id) ? 'selected' : '' }}>{{ $technik->nazwa }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-2">
                                                        <b-button variant="primary" size="sm" onclick="changeTechnik(Number($('#technik_select').val()))">Ok</b-button>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </b-col>
                        </b-row>
                    </template>
                </b-block>
            </b-col>
        </b-row>
        <b-row class="row-deck">
            <b-col lg="2">
                <b-block title="Urządzenie" theme="bg-primary-light" header-sm>
                    <template slot="content">
                        {{-- <table class="table table-sm table-borderless">
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
                                <td>{!! $zlecenie->urzadzenie->model ?: '<span class="font-w600 bg-danger text-white px-1">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Nr seryjny:</th>
                                <td>{!! $zlecenie->urzadzenie->nr_seryjny ?: '<span class="font-w600 bg-danger text-white px-1">uzupełnić</span>' !!}</td>
                            </tr>
                            <tr>
                                <th>Kod&nbsp;wyrobu:</th>
                                <td>{{ $zlecenie->urzadzenie->kod_wyrobu }}</td>
                            </tr>
                        </table> --}}

                        @if ($user->is_technik or ! $zlecenie->urzadzenie->id)
                            <div class="font-w700">Nazwa:</div>
                            <input type="text" class="form-control form-control-sm" value="{{ $zlecenie->urzadzenie->nazwa }}" disabled />

                            <div class="font-w700 mt-2">Producent:</div>
                            <input type="text" class="form-control form-control-sm" value="{{ $zlecenie->urzadzenie->producent }}" disabled />

                            <div class="font-w700 mt-2">Model:</div>
                            <input type="text" class="form-control form-control-sm" value="{{ $zlecenie->urzadzenie->model }}" disabled />

                            <div class="font-w700 mt-2">Nr seryjny:</div>
                            <input type="text" class="form-control form-control-sm" value="{{ $zlecenie->urzadzenie->nr_seryjny }}" disabled />

                            <div class="font-w700 mt-2">Kod wyrobu:</div>
                            <input type="text" class="form-control form-control-sm" value="{{ $zlecenie->urzadzenie->kod_wyrobu }}" disabled />
                        @else
                            <urzadzenie-inputs :small="true" :_urzadzenie="{{ json_encode($zlecenie->urzadzenie->only('id', 'producent', 'nazwa', 'model', 'kod_wyrobu', 'nr_seryjny', 'nr_seryjny_raw')) }}" />
                        @endif
                    </template>
                </b-block>
            </b-col>
            <b-col lg="10">
                <div class="block block-rounded shadow-sm">
                    <ul class="nav nav-tabs nav-tabs-alt align-items-center js-tabs bg-primary-light" data-toggle="tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#kosztorys" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)" onclick="focusKosztorysInput()">Kosztorys</a>
                        </li>
                        <li class="nav-item">
                            <a href="#opis" class="nav-link py-2 active show" style="color: rgba(255, 255, 255, 0.9)">Opis</a>
                        </li>
                        @if (! $user->is_technik)
                            <li class="nav-item">
                                <a href="#sms" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)">SMS</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#statusy" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)">Statusy</a>
                        </li>
                        <li class="nav-item">
                            {{-- <button type="button" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)" onclick="{{ $zlecenie->popup_zdjecia_link }}">Zdjęcia</button> --}}
                            <a href="#zdjecia" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)">Zdjęcia</a>
                        </li>
                        <li class="nav-item">
                            {{-- <button type="button" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)" onclick="{{ $zlecenie->popup_zdjecia_link }}">Zdjęcia</button> --}}
                            <a href="#tabliczka" class="nav-link py-2" style="color: rgba(255, 255, 255, 0.9)">Tabliczka</a>
                        </li>
                        @if ($user->is_desktop)
                            <li class="nav-item ml-auto">
                                <b-button-group size="sm" class="mr-2">
                                    @if ($user->is_technik)
                                        @if ($zlecenie->status->id == App\Models\Zlecenie\Status::NA_WARSZTACIE_ID)
                                            <zlecenie-change-status
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID)
                                                :remove_termin="0"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_WYJASNIENIA_ID))
                                            ></zlecenie-change-status>
                                            @if ($zlecenie->is_gwarancja)
                                                <zlecenie-change-status class="ml-1"
                                                    zlecenie_id=@json($zlecenie->id)
                                                    status_id=@json(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID)
                                                    :remove_termin="0"
                                                    name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                                    icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                                    color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_ZAMOWIENIA_ID))
                                                ></zlecenie-change-status>
                                            @else
                                                <zlecenie-change-status class="ml-1"
                                                    zlecenie_id=@json($zlecenie->id)
                                                    status_id=@json(App\Models\Zlecenie\Status::DO_WYCENY_ID)
                                                    :remove_termin="0"
                                                    name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                                    icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                                    color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_WYCENY_ID))
                                                ></zlecenie-change-status>
                                            @endif
                                            {{-- @if ($zlecenie->is_odplatne)
                                                <zlecenie-change-status class="ml-1"
                                                    zlecenie_id=@json($zlecenie->id)
                                                    status_id=@json(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID)
                                                    :remove_termin="0"
                                                    name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                                    icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                                    color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_POINFORMOWANIA_ID))
                                                ></zlecenie-change-status>
                                            @endif --}}
                                            @if ($zlecenie->was_warsztat)
                                                <zlecenie-change-status class="ml-1"
                                                    zlecenie_id=@json($zlecenie->id)
                                                    status_id=@json(App\Models\Zlecenie\Status::DO_ODBIORU_ID)
                                                    :remove_termin="0"
                                                    name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::DO_ODBIORU_ID))
                                                    icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_ODBIORU_ID))
                                                    color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_ODBIORU_ID))
                                                ></zlecenie-change-status>
                                            @else
                                                <zlecenie-change-status class="ml-1"
                                                    zlecenie_id=@json($zlecenie->id)
                                                    status_id=@json(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID)
                                                    :remove_termin="1"
                                                    name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                    icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                    color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                ></zlecenie-change-status>
                                            @endif
                                        @endif
                                    @elseif ( ! $user->is_technik)
                                        @if ( in_array($zlecenie->status->id, [
                                            App\Models\Zlecenie\Status::DO_ODBIORU_ID,
                                            App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID
                                        ]) )
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::DO_ODBIORU_ID)
                                                :remove_termin="0"
                                                :dont_change_color="1"
                                                name="Poinformowano o odbiorze"
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::DO_ODBIORU_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::DO_ODBIORU_ID))
                                            ></zlecenie-change-status>
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::NIE_ODBIERA_ID)
                                                second_status_id=@json(App\Models\Zlecenie\Status::DZWONIC_PO_ODBIOR_ID)
                                                :remove_termin="0"
                                                :dont_change_color="1"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::NIE_ODBIERA_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::NIE_ODBIERA_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::NIE_ODBIERA_ID))
                                            ></zlecenie-change-status>
                                        @endif

                                        @if (count($zlecenie->errors) > 0)
                                            <div class="ml-1">
                                                <b-button onclick="zatwierdzBlad()" size="sm" variant="light">
                                                    <i class="fa fa-exclamation-triangle text-danger"></i>
                                                    Usuń błąd
                                                </b-button>
                                            </div>
                                        @endif

                                        {{-- @if ($zlecenie->status->id == App\Models\Zlecenie\Status::ZAMOWIONO_CZESC_ID)
                                            <zlecenie-change-status class="ml-1"
                                                zlecenie_id=@json($zlecenie->id)
                                                status_id=@json(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID)
                                                :remove_termin="true"
                                                name=@json(App\Models\Zlecenie\Status::getName(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                icon=@json(App\Models\Zlecenie\Status::getIcon(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))
                                                color=@json(App\Models\Zlecenie\Status::getColor(App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID))></zlecenie-change-status>
                                        @endif --}}
                                    @endif
                                </b-button-group>
                            </li>
                        @endif
                    </ul>
                    <div class="block-content tab-content overflow-hidden block-content-full">
                        <div class="tab-pane fade" id="kosztorys" role="tabpanel">
                            @if (false)
                                <table class="table table-sm table-striped table-vcenter font-size-sm">
                                    <thead>
                                        <tr>
                                            <th class="font-w700" nowrap>Symbol dost.</th>
                                            <th class="font-w700" nowrap>Symbol</th>
                                            <th class="font-w700" nowrap>Nazwa</th>
                                            <th class="font-w700" nowrap>Opis</th>
                                            <th class="font-w700 text-right" nowrap>Cena brutto</th>
                                            <th class="font-w700 text-center" nowrap>Ilość</th>
                                            {{-- <th class="font-w700 text-right" nowrap>Wartość netto</th> --}}
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
                                                {{-- <td class="text-right" nowrap>{{ $pozycja->wartosc_formatted }}</td> --}}
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
                                            {{-- <th class="text-right">{{ number_format($wartosc_netto, 2, '.', ' ') }} zł</th> --}}
                                            <th class="text-right">{{ number_format($wartosc_brutto, 2, '.', ' ') }} zł</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <zlecenie-kosztorys _token=@json(csrf_token()) :zlecenie_id="{{ $zlecenie->id }}" :is_technik="{{ $user->is_technik ? 'true' : 'false' }}" :technik_symbols='@json(App\Models\Zlecenie\Zlecenie::getTechnikSymbols($user->technik_id ?? 0))'></zlecenie-kosztorys>
                            @endif
                        </div>
                        <div class="tab-pane fade active show" id="opis" role="tabpanel">
                            @if ($zlecenie->errors)
                                <div class="font-w600 text-danger">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    {{ implode(', ', $zlecenie->errors) }}
                                </div>
                            @endif
                            <zlecenie-opis zlecenie_id=@json($zlecenie->id) :is_technik="{{ (int) $user->is_technik }}" />
                        </div>
                        <div class="tab-pane fade" id="sms" role="tabpanel">
                            <sms-create _token=@json(csrf_token()) :_predefined="true" :_telefony="{{ json_encode($zlecenie->klient->telefony_array) }}" :_footer='@json(App\Sms::FOOTER)' :zlecenie_id="{{ $zlecenie->id }}" :zlecenie_status_id="{{ $zlecenie->status_id }}" :smses='@json($zlecenie->smses)' />
                        </div>
                        <div class="tab-pane fade" id="statusy" role="tabpanel">
                            <b-row>
                                <b-col cols="12" xl="8">
                                    <div class="table-responsive">
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
                                                @foreach ($zlecenie->statusy as $status_historia)
                                                    @php
                                                        $status = $status_historia->status;
                                                    @endphp
                                                    <tr>
                                                        <td nowrap class="align-middle {{ $status->color ? 'table-' . $status->color : '' }}">
                                                            <i class="{{ $status->icon }} {{ $status->color ? 'text-' . $status->color : '' }} mx-2"></i>
                                                            {{ $status->nazwa }}
                                                        </td>
                                                        <td nowrap>{{ $status_historia->data }}</td>
                                                        <td nowrap>{{ $status_historia->pracownik->nazwa }}</td>
                                                        @role('super-admin')
                                                            <td nowrap>
                                                                <button onclick="removeStatus(this,{{ $status_historia->id }})" type="button" class="btn btn-sm btn-rounded btn-danger">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </td>
                                                        @endrole
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </b-col>
                            </b-row>
                        </div>
                        <div class="tab-pane fade" id="zdjecia" role="tabpanel">
                            @include('zlecenie-zdjecie.component.index', compact('zlecenie'))
                            {{-- <Zdjecia _token=@json(csrf_token()) :zlecenie_id=@json($zlecenie->id) /> --}}
                        </div>
                        <div class="tab-pane fade" id="tabliczka" role="tabpanel">
                            <zlecenie-tabliczka _token=@json(csrf_token()) :zlecenie_id=@json($zlecenie->id) :zdjecia='@json($zlecenie->zdjecia->filter(function ($zdjecie) { return $zdjecie->type == 'tabliczka'; }))' />
                        </div>
                    </div>
                </div>
            </b-col>
        </b-row>
    </div>

    <div class="modal" id="zleceniodawca-modal" tabindex="-1" role="dialog" aria-labelledby="zleceniodawca-modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Zleceniodawca</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <input type="text" class="form-control" value="{{ @$zlecenie->kosztorys_opis->opis ?? '' }}">
                    </div>
                    <div class="block-content block-content-full text-right bg-light">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Anuluj</button>
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal" onclick="changeZleceniodawca($('#zleceniodawca-modal input').val())">Zapisz</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="nrobcy-modal" tabindex="-1" role="dialog" aria-labelledby="nrobcy-modal" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Nr obcy</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <input type="text" class="form-control" value="{{ @$zlecenie->nr_obcy ?? '' }}">
                    </div>
                    <div class="block-content block-content-full text-right bg-light">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Anuluj</button>
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal" onclick="changeNrobcy($('#nrobcy-modal input').val())">Zapisz</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')<script>

$(document).keydown(function (e) {
	if (e.keyCode == 27) {
		window.close();
	}
});

let last_status_id = @json($zlecenie->status_id);
const statusy_removable_termin = [14, 13, 29];
const IS_UP_TO_DATE_TERMIN = {{ $is_up_to_date_termin ? 'true' : 'false' }};

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
    if (statusy_removable_termin.includes(status_id)) {
        remove_termin = 1;
    }
    if (status_id == 14 && IS_UP_TO_DATE_TERMIN) {
        remove_termin = 0;
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

var last_technik_id = @json($zlecenie->technik_id);
function changeTechnik(technik_id) {
    if (last_technik_id == technik_id) {
        swal({
            position: 'center',
            type: 'error',
            title: 'Już ustawiono tego technika',
            showConfirmButton: false,
            timer: 1500,
        });
        return;
    }

    last_technik_id = technik_id;

    axios.post( route('zlecenia.api.change_technik', {
        id: @json($zlecenie->id),
        technik_id,
    })).then((response) => {
        swal({
            position: 'center',
            type: 'success',
            title: 'Technik zmieniony',
            showConfirmButton: false,
            timer: 1500,
        });
    });
}

function changeZleceniodawca(zleceniodawca) {
    axios.post( route('zlecenia.api.change_data', {
        id: @json($zlecenie->id),
        type: 'zleceniodawca',
    }), {
        zleceniodawca,
    }).then((response) => {
        location.reload()
    })
}

function changeNrobcy(nrobcy) {
    axios.post( route('zlecenia.api.change_data', {
        id: @json($zlecenie->id),
        type: 'nrobcy',
    }), {
        // _token: @json(csrf_token()),
        nrobcy,
    }).then((response) => {
        location.reload()
    })
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

function focusKosztorysInput() {
    setTimeout(() => {
        $('#kosztorys form > input').focus()
    }, 300)
}

</script>@append
