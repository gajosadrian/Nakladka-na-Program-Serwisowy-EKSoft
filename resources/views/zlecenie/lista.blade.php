@extends('global.app')
@section('datatable_literal_search', true)
@php
    $user = auth()->user();
    $room = rand();
@endphp
@if ($autorefresh)
    @section('autorefresh', $autorefresh)
@endif

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zlecenia</h1>
            </div>
       </div>
    </div>

    <div class="content">
        @if ($show_errors and $zlecenia_duplicate->count() > 0)
            <b-block title="Zdublowane zlecenia" theme="bg-danger">
                <template slot="content">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover table-vcenter font-size-sm">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="font-w700">Nr klienta</th>
                                    <th class="font-w700">Nazwa</th>
                                    <th class="font-w700">Miasto</th>
                                    <th class="font-w700">Nr zlecenia</th>
                                    <th class="font-w700">Nr obcy</th>
                                    <th class="font-w700">Urządzenie</th>
                                    <th class="font-w700">Nr seryjny</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zlecenia_duplicate as $zlecenie_duplicate)
                                    @for ($i = 0; $i <= 1; $i++)
                                        @php
                                            $zlecenie = $zlecenie_duplicate[$i];
                                        @endphp
                                        <tr>
                                            <td nowrap>{{ $zlecenie->klient->symbol }}</td>
                                            <td nowrap>{{ str_limit($zlecenie->klient->nazwa, 30) }}</td>
                                            <td nowrap>{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto_short }}</td>
                                            <td class="font-w600" nowrap><a href="javascript:void(0)" onclick="{{ $zlecenie->popup_link }}" style="cursor:pointer">{{ $zlecenie->nr }}</a></td>
                                            <td class="font-w700 text-danger" nowrap>{{ $zlecenie->nr_obcy }}</td>
                                            <td nowrap>{{ $zlecenie->urzadzenie->producent }}, {{ $zlecenie->urzadzenie->nazwa }}</td>
                                            <td nowrap>{{ $zlecenie->urzadzenie->nr_seryjny }}</td>
                                        </tr>
                                    @endfor
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </template>
            </b-block>
        @endif

        <b-block>
            <template slot="content">
                <div class="row">
                    <div class="col-lg-2">
                        <b-button variant="primary" size="sm" onclick="location.reload()">
                            <i class="fa fa-sync-alt"></i>
                            Odśwież
                        </b-button>
                    </div>
                    <div class="col-lg-2">
                        Zleceń realizowanych: <span class="font-w600 text-primary">{{ $zlecenia_realizowane_n }}</span>
                        <br>
                        Zleceń do zamknięcia: <span class="font-w600 text-primary">{{ $zlecenia_ukonczone_n }}</span>
                    </div>
                    <div class="col-lg-8">
                        Ilość błędów:
                        <span style="cursor: pointer;" onclick="szukajBledow()">
                            @if ($errors_n > 5)
                                <span class="font-w600 bg-danger text-white px-1">{{ $errors_n }}</span>
                            @else
                                <span class="font-w600 text-danger">{{ $errors_n }}</span>
                            @endif
                        </span>
                        <br>
                        <b-button variant="info" size="sm" pill onclick="jQuery('#bledy-modal').modal('show')">
                            &nbsp;<i class="fa fa-info"></i>&nbsp;
                        </b-button>
                        {{-- <span class="{{ ($niesprawdzone_czesci_n > 0) ? 'bg-danger text-white px-1' : '' }}">Niesprawdzonych części: {{ $niesprawdzone_czesci_n }}</span> --}}
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="zlecenia{{ $room }}" class="table table-sm table-striped table-hover table-vcenter font-size-sm dataTable">
						<thead>
							<tr class="text-uppercase">
								<th class="font-w700">Lp.</th>
								<th class="font-w700">Nazwa</th>
								<th class="font-w700">Adres</th>
                                @if ($show_errors)
                                    <th class="font-w700"></th>
                                @endif
								<th class="font-w700">Nr zlecenia</th>
                                <th class="font-w700"></th>
								<th class="font-w700">Urządzenie</th>
								<th class="font-w700">Status</th>
                                {{-- <th class="font-w700">Ostatnia data</th> --}}
								<th class="font-w700">Przyjęcie</th>
								<th class="font-w700">Termin</th>
								<th class="d-none"></th>
							</tr>
						</thead>
						<tbody>
							@php $counter = 0 @endphp
							@foreach ($zlecenia as $zlecenie)
								{{-- <tr class="{{ ($zlecenie->is_akc_kosztow and $zlecenie->is_warsztat) ? 'table-success' : '' }}"> --}}
                                <tr class="
                                    @if ($zlecenie->is_akc_kosztow and $zlecenie->is_warsztat)
                                        {{-- table-success --}}
                                    @elseif (! $user->is_technik and @$zlecenie->last_sms->type == 'error')
                                        table-danger
                                    @endif
                                ">
									<th class="text-muted">{{ ++$counter }}</th>
									<td nowrap>
                                        @if ($zlecenie->is_akc_kosztow and $zlecenie->is_warsztat)
                                            {{-- <i class="fa fa-check-circle text-success"></i> --}}
                                        @elseif (! $user->is_technik and @$zlecenie->last_sms->type == 'error')
                                        <i class="fa fa-envelope text-danger"></i>
                                        @endif
										{{ str_limit($zlecenie->klient->nazwa, 25) }}<br>
										<small class="text-muted">({{ $zlecenie->klient->symbol }})</small>
									</td>

									<td nowrap>
										{{ $zlecenie->klient->adres }}<br>
										{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto_short }}
									</td>

                                    @if ($show_errors)
    									{{-- <td class="{{ (count($zlecenie->errors) > 0) ? 'table-danger' : '' }} font-small">
                                            <ul class="list-unstyled mb-0 d-none">
        										@foreach ($zlecenie->errors as $error)
                                                    <li>
                                                        <i class="fa fa-exclamation-triangle text-danger"></i>
            											{{ $error }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td> --}}
                                        <td class="text-center">
                                            @if ($zlecenie->has_errors)
                                                <div class="d-none">
                                                    błąd
                                                    {{ implode(' ', $zlecenie->errors) }}
                                                </div>
                                                <i
                                                    class="fa fa-exclamation-triangle text-danger"
                                                    data-toggle="tooltip" data-placement="left"
                                                    title="{{ implode('. ', $zlecenie->errors) }}"
                                                ></i>
                                            @endif
    									</td>
                                    @endif

                                    {!! $zlecenie->tableCellNrHTML !!}

                                    <td class="text-center">
                                        @if ($zlecenie->is_na_warsztacie)
                                            <i
                                                class="fa fa-home text-warning"
                                                data-toggle="tooltip" data-placement="left"
                                                title="Na warsztacie"
                                            ></i>
                                        @endif
                                    </td>

									<td nowrap>
										{{ $zlecenie->urzadzenie->nazwa }}<br>
										{{ $zlecenie->urzadzenie->producent }}
									</td>

                                    {{-- {!! $zlecenie->tableCellStatusHTML !!} --}}
                                    <td nowrap>
                                        <span class="rounded p-2 table-{{ $zlecenie->status->color }}">
                                            <i class="{{ $zlecenie->status->icon }} text-{{ $zlecenie->status->color }}"></i>
                                            {{ $zlecenie->status->nazwa }}
                                        </span>
                                    </td>

									<td nowrap>
                                        {{-- @if ($zlecenie->is_termin)
                                            {{ $zlecenie->data_zakonczenia_formatted }}
                                        @else
                                            {{ $zlecenie->data_statusu_formatted }}
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            @php
                                                if ($zlecenie->is_termin) {
                                                    $dni_od_zakonczenia = $zlecenie->dni_od_zakonczenia;
                                                    $is_termin = true;
                                                } else {
                                                    $dni_od_zakonczenia = $zlecenie->dni_od_statusu;
                                                    $is_termin = false;
                                                }
                                            @endphp
    										@if ($dni_od_zakonczenia > 0)
    											@if ($dni_od_zakonczenia >= 2)
    												({{ $dni_od_zakonczenia }} dni temu)
    											@else
    												(wczoraj)
    											@endif
                                            @elseif ($dni_od_zakonczenia == 0 and $is_termin)
                                                (dzisiaj)
    										@endif
                                        </small> --}}

                                        {{ $zlecenie->data_przyjecia->format('Y-m-d') }}
                                        <br>
                                        @php
                                            $dni_od_przyjecia = $zlecenie->dni_od_przyjecia;
                                        @endphp
                                        <small class="text-muted">
                                            @if ($dni_od_przyjecia > 0)
                                                @if ($dni_od_przyjecia >= 2)
                                                    ({{ $dni_od_przyjecia }} dni temu)
                                                @else
                                                    (wczoraj)
                                                @endif
                                            @else
                                                (dzisiaj)
                                            @endif
                                        </small>
                                    </td>

                                    <td nowrap>
                                        @if ($zlecenie->is_termin)
                                            {{ $zlecenie->data_zakonczenia_formatted }}
                                        @elseif(in_array($zlecenie->status_id, [\App\Models\Zlecenie\Status::GOTOWE_DO_WYJAZDU_ID, \App\Models\Zlecenie\Status::NIE_ODBIERA_ID, \App\Models\Zlecenie\Status::PONOWNA_WIZYTA_ID, \App\Models\Zlecenie\Status::ZLECENIE_WPISANE_ID]))
                                            <div class="d-none">
                                                ustal termin
                                            </div>
                                        @endif
                                    </td>

									<td class="d-none">
                                        {{ $zlecenie->nr }} {{ $zlecenie->nr_obcy }}
                                        {{ $zlecenie->klient->telefony_formatted }}
                                        {{ str_replace(['-', ' '], '', $zlecenie->klient->telefony_formatted) }}
                                        @if ($zlecenie->is_technik)
                                            technik:{{ $zlecenie->technik->akronim }}
                                        @endif
										@foreach ($zlecenie->kosztorys_pozycje as $pozycja)
                                            @continue($pozycja->is_usluga)
											{{ $pozycja->opis }}
                                            {{ $pozycja->towar->symbol }}
                                            {{ $pozycja->towar->symbol_dostawcy }}
                                            @if ($pozycja->towar->symbol_dostawcy != $pozycja->towar->symbol_dostawcy_min)
                                                {{ $pozycja->towar->symbol_dostawcy_min }}
                                            @endif
                                            {{ $pozycja->towar->symbol_dostawcy2 }}
                                            {{ $pozycja->towar->opis }}
										@endforeach
									</td>
								</tr>
							@endforeach
						</tbody>
                    </table>
                </div>
            </template>
        </b-block>
    </div>

    <div class="modal" id="bledy-modal" tabindex="-1" role="dialog" aria-labelledby="bledy-modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Błędy do zleceń otwartych</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <ul>
                            <li><strong>Ustal termin</strong> – komunikat po 3 dniach. Można wyszkuwać wszystkie zlecenia bez terminu po tej nazwie błędu.</li>
                            <li><strong>Brak reakcji</strong> – po 2 dniach od statusów Umówiono, Gotowe do wyjazdu, Na warsztacie, Preautoryzacja, Na warsztacie</li>
                            <li><strong>Dzwonić do klienta</strong> – po 1 dniu od statusu Poinformować o kosztach</li>
                            <li><strong>Akc. kosztów? Dzwonić</strong> – po 3 dniach od statusu Informacja o kosztach</li>
                            <li><strong>Brak reakcji</strong> – po 1 dniu od statusów Do zamówienia, Do wyceny</li>
                            <li><strong>Brak reakcji</strong> – po 3 dniach od statusu Do wyjaśnienia</li>
                            <li><strong>Zaliczka</strong> – po 3 dniach od statusu Zaliczka</li>
                            <li><strong>Dzwonić po odbiór</strong> – po 1 dniu od statusu Dzwonić po odbiór</li>
                            <li><strong>Dzwonić po odbiór</strong> – po 7 dniach od statusu Do odbioru</li>
                            <li><strong>Nierozliczone</strong> – po 7 dniach od statusu Do rozliczenia</li>
                            <li><strong>Co z częścią?</strong> – po 7 dniach od statusu Zamówiono część</li>
                        </ul>
                    </div>
                    <div class="block-content block-content-full text-right bg-light">
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Zamknij</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_after')<script>$(function(){

var $lastRow = null;
$('table#zlecenia{{ $room }} > tbody tr:not(#noclicable)').click(function () {
    let $row = $(this);

    if ($lastRow) {
        $lastRow.removeClass('table-info');
    }
    $row.addClass('table-info');

    $lastRow = $row;
});

var $searchInput = $('div#zlecenia{{ $room }}_filter input[type=search]');
let searchValue = @json($search_value);
var searchValue_last = @json($search_value);
setInterval(function() {
    let value = $searchInput.val();

    if (searchValue_last == value) return;

    $.post(route('api.save_field'), {
        _token: '{{ csrf_token() }}',
        _method: 'put',
        name: 'zlecenia.search',
        value: value
    })
    .done(function (data) {
        searchValue_last = value;
    });
}, 5000)

szukajBledow = () => {
    $('table#zlecenia{{ $room }}').DataTable().data().search('błąd').draw();
}

if (searchValue) {
    $('table#zlecenia{{ $room }}').DataTable().data().search(searchValue).draw();
}

})</script>@endsection
