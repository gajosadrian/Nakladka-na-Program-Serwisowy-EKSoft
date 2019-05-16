@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Kilometrówka</h1>
            </div>
        </div>
    </div>

    <div class="content">
		{{-- <b-block full class="d-print-none">
            <template slot="content">
                <button type="button" class="btn btn-sm btn-rounded btn-primary" onclick="toggleFullScreen()">Pełny ekran</button>
            </template>
        </b-block> --}}

        <b-block title="Parametry" full class="d-print-none">
            <template slot="content">
                @if (! $is_technik)
                    <div class="mb-3">
                        @foreach ($technicy as $_technik)
                            <b-link href="{{ route('zlecenia.kilometrowka', [ 'technik_id' => $_technik->id, 'month_id' => $month_id ]) }}" class="btn btn-outline-primary {{ ($_technik->id == $technik_id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                        @endforeach
                    </div>
                @endif

                <b-row>
                    @if ($technik)
                        <b-col cols="2">
                            <select class="form-control" onchange="updateUrl(this)">
                                <option value="0">Poprzedni miesiąc</option>
                                @foreach ($months as $month)
                                    <option value="{{ $month->id }}" {{ ($month->id == $month_id) ? 'selected' : '' }}>{{ $month->name }}</option>
                                @endforeach
                            </select>
                        </b-col>
                        <b-col cols="2">
                            <b-button class="btn-rounded shadow" variant="info" size="sm" onclick="Dashmix.helpers('print')">
                                <i class="fa fa-print"></i> Drukuj
                            </b-button>
                        </b-col>
                    @endif
                </b-row>
            </template>
        </b-block>

        @if ($technik)
            <b-block title="Zlecenia" noprint>
                <template slot="content">
					<div class="mx-3">
                        <div class="mb-3 clearfix" style="font-size: 2em">
                            <div class="float-left">
                                {{ $technik->nazwa }} - {{ $month->name }}
                                <span class="font-size-sm text-muted">(<span id="zlecenia_n"></span> zlec.)</span>
                            </div>
                            <div class="float-right">
                                <b-img src="{{ asset('media/dargaz-logo.png') }}" alt="logo"></b-img>
                            </div>
                        </div>

						<table class="table table-sm table-vcenter">
							<thead>
								<tr class="text-uppercase">
									<th class="font-w700">Data</th>
									<th class="font-w700">Zlecenie</th>
									<th class="font-w700">Miasto</th>
									<th class="font-w700">Adres</th>
								</tr>
							</thead>
							<tbody>
                                @php
                                    $zlecenia_n = 0;
                                @endphp
								@foreach ($grouped_terminy as $date_string => $grouped_termin)

									<tr>
										<td class="bg-gray font-w700" colspan="4">{{ $date_string }}</td>
									</tr>

									@foreach ($grouped_termin as $termin)
										@php
											$zlecenie = $termin->zlecenie;
										@endphp
										@if ($zlecenie->id and !$zlecenie->was_warsztat and !$zlecenie->is_odwolano)
                                            @php
                                                $zlecenia_n++;
                                            @endphp
											<tr>
												<th class="text-right">{{ $zlecenia_n }}.</th>
												<td><span onclick="{{ $zlecenie->popup_link }}" style="cursor:pointer">{{ $zlecenie->nr }}</span> {{ $zlecenie->status_id }}</td>
												<td>{{ $zlecenie->klient->kod_pocztowy }} {{ $zlecenie->klient->miasto }}</td>
												<td>{{ $zlecenie->klient->adres }}</td>
											</tr>
										@elseif ($zlecenie->id and $zlecenie->was_warsztat)
											<tr>
												<td></td>
												<td><span onclick="{{ $zlecenie->popup_link }}" style="cursor:pointer">{{ $zlecenie->nr }}</span> {{ $zlecenie->status_id }}</td>
												<td colspan="2"><i>Warsztat</i></td>
											</tr>
										@elseif ($termin->temat)
											<tr>
												<td></td>
												<td colspan="3"><i>{{ $termin->temat }}</i></td>
											</tr>
										@endif
									@endforeach
								@endforeach
							</tbody>
						</table>
					</div>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

@if ($technik)
	$(function(){
		$('#zlecenia_n').text({{ $zlecenia_n }});
	})
@endif

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('zlecenia.kilometrowka', {
        technik_id: @json($technik_id),
        month_id: value,
    }));
}

</script>@endsection
