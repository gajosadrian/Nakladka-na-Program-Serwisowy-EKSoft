@extends('global.app')

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zlecenia</h1>
            </div>
       </div>
    </div>

    <div class="content">
        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover table-vcenter font-size-sm dataTable">
						<thead>
							<tr class="text-uppercase">
								<th class="font-w700">Lp.</th>
								<th class="font-w700">Nazwisko i imię</th>
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
								<tr>
									<td class="text-muted">{{ ++$counter }}</td>
									<td>
										{{ $zlecenie->klient->nazwa }}<br>
										<small class="text-muted">({{ $zlecenie->klient->symbol }})</small>
									</td>

									<td>
										adres<br>
										00-000 miasto
									</td>

									<td class="font-w600">
										<a href="javascript:void(0)" onclick="PopupCenter('{{ route('zlecenia.show', $zlecenie->id) }}', 'zlecenie{{ $zlecenie->id }}', 1500, 700)">
                                            <i class="{{ $zlecenie->znacznik->icon }} mr-2"></i>
											{{ $zlecenie->nr_or_obcy }}
										</a>
                                        <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{{ $zlecenie->nr }}'">
                                            <i class="far fa-copy"></i>
                                        </a>
									</td>

									<td>
										{{ $zlecenie->urzadzenie->nazwa }}<br>
										{{ $zlecenie->urzadzenie->producent }}
									</td>

                                    {!! $zlecenie->tableCellStatusHTML !!}

									<td class="text-danger font-small">
										@foreach ($zlecenie->errors as $error)
											{{ $error }}
										@endforeach
									</td>

									<td>
										{{ $zlecenie->data_zakonczenia_formatted }}<br>
                                        <small class="text-muted">
    										@if ($zlecenie->dni_od_zakonczenia > 0)
    											@if ($zlecenie->dni_od_zakonczenia >= 2)
    												({{ $zlecenie->dni_od_zakonczenia }} dni temu)
    											@else
    												(wczoraj)
    											@endif
                                            @elseif ($zlecenie->dni_od_zakonczenia == 0 and $zlecenie->is_termin)
                                                (dzisiaj)
    										@endif
                                        </small>
									</td>

									<td class="d-none">
										{{ $zlecenie->nr }} ; {{ $zlecenie->nr_obcy }}
										@foreach ($zlecenie->kosztorys_pozycje as $pozycja)
											; {{ $pozycja->opis }} ; {{ $pozycja->towar->symbol }} ; {{ $pozycja->towar->symbol_dostawcy }}
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
