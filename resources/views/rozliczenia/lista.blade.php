@extends('global.app')
@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rozliczenia</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block full>
            <template slot="content">
                <b-row>
                    <b-col cols="6">
                        <canvas id="wykresRobocizn" height="400" style="width:100%"></canvas>
                    </b-col>
                    <b-col cols="6"></b-col>
                </b-row>
            </template>
        </b-block>

        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-vcenter font-size-sm">
						<thead>
							<tr class="text-uppercase">
                                <th class="font-w700">Okres</th>
                                <th class="font-w700">Ilość zleceń</th>
                                <th class="font-w700 text-right" v-b-tooltip.hover title="Netto">Suma robocizn</th>
                                <th class="font-w700 text-right" v-b-tooltip.hover title="Netto">Suma dojazdów</th>
                                <th class="font-w700">Zamknięcie</th>
                                <th class="font-w700">Rozliczył</th>
                                <th class="font-w700">Działania</th>
							</tr>
						</thead>
						<tbody>
                            @if ($is_creatable)
                                <tr>
                                    <td style="width:200px">
                                        <b-form action="{{ route('rozliczenia.store') }}" method="post">
                                            {{ csrf_field() }}
                                            <b-form-row>
                                                <b-col cols="6">
                                                    <b-input @focus="$event.target.select()" type="text" name="rok" placeholder="Rok" size="sm" value="{{ $creatable_date->format('Y') }}"></b-input>
                                                </b-col>
                                                <b-col cols="6">
                                                    <b-input @focus="$event.target.select()" type="text" name="miesiac" placeholder="Miesiąc" size="sm" value="{{ $creatable_date->format('n') }}"></b-input>
                                                </b-col>
                                                <b-button id="storeRozliczenie" type="submit" hidden></b-button>
                                            </b-form-row>
                                        </b-form>
                                    </td>
                                    <td>-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="$('#storeRozliczenie').click()" v-b-tooltip.hover title="Utwórz nowe rozliczenie">
                                            <i class="fa fa-plus-circle"></i> Utwórz
                                        </a>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($rozliczenia ?? [] as $rozliczenie)
                                <tr>
                                    <th>{{ $rozliczenie->nr }}</th>
                                    <td class="text-muted">{{ $rozliczenie->rozliczone_zlecenia->count() }}</td>
                                    <td class="text-muted text-right">{{ $rozliczenie->suma_robocizn_formatted }}</td>
                                    <td class="text-muted text-right">{{ $rozliczenie->suma_dojazdow_formatted }}</td>
                                    <td class="text-muted">{!! $rozliczenie->is_closed ? $rozliczenie->closed_at->format('Y-m-d') : '<i class="text-success font-w600">w trakcie rozliczania</i>' !!}</td>
                                    <td class="text-muted">{{ $rozliczenie->is_closed ? $rozliczenie->rozliczyl : '-' }}</td>
                                    <td>
                                        <a href="{{ route('rozliczenia.pokaz', [ 'id' => $rozliczenie->id ]) }}" class="font-w600">
                                            @if ($rozliczenie->is_closed)
                                                <i class="far fa-eye"></i> Zobacz
                                            @else
                                                <i class="fa fa-pencil-alt"></i> Edytuj
                                            @endif
                                        </a>
                                        <a href="{{ route('rozliczenia.analiza', [ 'id' => $rozliczenie->id ]) }}" class="font-w600 ml-3">
                                            <i class="fa fa-align-left"></i> Analiza
                                        </a>
										@if (!$rozliczenie->is_closed)
											<a href="javascript:void(0)" class="text-danger font-w600 ml-3">
												<i class="fa fa-lock"></i> Zakończ rozliczenie
											</a>
										@endif
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

var wykresRobocizn = new Chart(document.getElementById('wykresRobocizn').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['MAR', 'KWI', 'MAJ', 'CZE', 'LIP', 'SIE', 'WRZ', 'PAŹ', 'LIS', 'GRU', 'STY', 'LUT'],
        datasets: [{
            label: 'Suma robocizn',
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 23380, 25099],
            borderWidth: 1,
            backgroundColor: '#64b5f6',
            borderColor: '#2286c3',
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        },
    },
});

})</script>@endsection
