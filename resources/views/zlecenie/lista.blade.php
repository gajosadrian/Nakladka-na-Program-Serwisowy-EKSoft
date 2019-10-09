@extends('global.app')
@php
    $room = rand();
@endphp
@if ($autorefresh)
    @section('autorefresh', 300)
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
        @if ($zlecenia_duplicate->count() > 0)
            <b-block title="Zdublowane zlecenia" theme="bg-danger">
                <template slot="content">
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
                                        <td nowrap>{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}</td>
                                        <td class="font-w600" nowrap><a href="javascript:void(0)" onclick="{{ $zlecenie->popup_link }}" style="cursor:pointer">{{ $zlecenie->nr }}</a></td>
                                        <td class="font-w700 text-danger" nowrap>{{ $zlecenie->nr_obcy }}</td>
                                        <td nowrap>{{ $zlecenie->urzadzenie->producent }}, {{ $zlecenie->urzadzenie->nazwa }}</td>
                                        <td nowrap>{{ $zlecenie->urzadzenie->nr_seryjny }}</td>
                                    </tr>
                                @endfor
                            @endforeach
                        </tbody>
                    </table>
                </template>
            </b-block>
        @endif

        <b-block>
            <template slot="content">
                <div class="">
                    <b-button variant="success" size="sm" onclick="location.reload()">
                        <i class="fa fa-sync-alt"></i>
                        Odśwież
                    </b-button>
                </div>

                <div class="table-responsive">
                    <table id="zlecenia{{ $room }}" class="table table-sm table-striped table-hover table-vcenter font-size-sm dataTable">
						<thead>
							<tr class="text-uppercase">
								<th class="font-w700">Lp.</th>
								<th class="font-w700">Nazwa</th>
								<th class="font-w700">Adres</th>
								<th class="font-w700">Nr zlecenia</th>
								<th class="font-w700">Urządzenie</th>
								<th class="font-w700">Status</th>
								<th class="font-w700">Błędy</th>
								<th class="font-w700">Ostatnia data</th>
								<th class="d-none"></th>
							</tr>
						</thead>
						<tbody>
							@php $counter = 0 @endphp
							@foreach ($zlecenia as $zlecenie)
								<tr class="{{ ($zlecenie->is_akc_kosztow and $zlecenie->is_warsztat) ? 'table-warning' : '' }}">
									<th class="text-muted">{{ ++$counter }}</th>
									<td nowrap>
                                        @if ($zlecenie->is_akc_kosztow and $zlecenie->is_warsztat)
                                            <i class="fa fa-check-circle text-success"></i>
                                        @endif
										{{ str_limit($zlecenie->klient->nazwa, 30) }}<br>
										<small class="text-muted">({{ $zlecenie->klient->symbol }})</small>
									</td>

									<td nowrap>
										{{ $zlecenie->klient->adres }}<br>
										{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}
									</td>

									{!! $zlecenie->tableCellNrHTML !!}

									<td nowrap>
										{{ $zlecenie->urzadzenie->nazwa }}<br>
										{{ $zlecenie->urzadzenie->producent }}
									</td>

                                    {!! $zlecenie->tableCellStatusHTML !!}

									<td class="{{ (count($zlecenie->errors) > 0) ? 'table-danger' : '' }} font-small">
                                        <ul class="list-unstyled mb-0">
    										@foreach ($zlecenie->errors as $error)
                                                <li>
                                                    <i class="fa fa-exclamation-triangle text-danger"></i>
        											{{ $error }}
                                                </li>
                                            @endforeach
                                        </ul>
									</td>

									<td nowrap>
                                        @if ($zlecenie->is_termin)
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
                                        </small>
									</td>

									<td class="d-none">
                                        {{ $zlecenie->nr }} {{ $zlecenie->nr_obcy }}
                                        @if ($zlecenie->is_technik)
                                            technik:{{ $zlecenie->technik->akronim }}
                                        @endif
										@foreach ($zlecenie->kosztorys_pozycje as $pozycja)
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

    if (searchValue) {
        $('table#zlecenia{{ $room }}').DataTable().data().search(searchValue).draw();
    }
})</script>@endsection
