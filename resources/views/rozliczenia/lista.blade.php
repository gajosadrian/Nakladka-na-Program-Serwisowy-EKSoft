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
                    <b-col col lg="6">
                        <canvas id="wykresRobocizn" height="400" style="width:100%"></canvas>
                    </b-col>
                    <b-col col lg="6">
                        <canvas id="wykresDojazdow" height="400" style="width:100%"></canvas>
                    </b-col>
                </b-row>
            </template>
        </b-block>

        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-vcenter font-size-sm">
						<thead>
							<tr class="text-uppercase">
                                <th class="font-w700" nowrap>Okres</th>
                                <th class="font-w700" nowrap>Ilość zleceń</th>
                                <th class="font-w700 text-right" v-b-tooltip.hover title="Netto" nowrap>Suma robocizn</th>
                                <th class="font-w700 text-right" v-b-tooltip.hover title="Netto" nowrap>Suma dojazdów</th>
                                <th class="font-w700" nowrap>Zamknięcie</th>
                                <th class="font-w700" nowrap>Rozliczył</th>
                                <th class="font-w700" nowrap>Działania</th>
							</tr>
						</thead>
						<tbody>
                            @if ($is_creatable)
                                <tr>
                                    <td style="width:200px" nowrap>
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
                                    <td nowrap>-</td>
                                    <td class="text-right" nowrap>-</td>
                                    <td class="text-right" nowrap>-</td>
                                    <td nowrap>-</td>
                                    <td nowrap>-</td>
                                    <td nowrap>
                                        <a href="javascript:void(0)" onclick="$('#storeRozliczenie').click()" v-b-tooltip.hover title="Utwórz nowe rozliczenie">
                                            <i class="fa fa-plus-circle"></i> Utwórz
                                        </a>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($rozliczenia ?? [] as $rozliczenie)
                                <tr>
                                    <th nowrap>{{ $rozliczenie->nr }}</th>
                                    <td class="text-muted" nowrap>{{ $rozliczenie->rozliczone_zlecenia->count() }}</td>
                                    <td class="text-muted text-right" nowrap>{{ $rozliczenie->suma_robocizn_formatted }}</td>
                                    <td class="text-muted text-right" nowrap>{{ $rozliczenie->suma_dojazdow_formatted }}</td>
                                    <td class="text-muted" nowrap>{!! $rozliczenie->is_closed ? $rozliczenie->closed_at->format('Y-m-d') : '<i class="text-success font-w600">w trakcie rozliczania</i>' !!}</td>
                                    <td class="text-muted" nowrap>{{ $rozliczenie->is_closed ? $rozliczenie->rozliczyl : '-' }}</td>
                                    <td nowrap>
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

@foreach ([
    [
        'name' => 'Suma robocizn',
        'el' => 'wykresRobocizn',
        'var' => 'suma_robocizn',
        'backgroundColor' => '#64b5f6',
        'borderColor' => '#2286c3',
    ],
    [
        'name' => 'Suma dojazdów',
        'el' => 'wykresDojazdow',
        'var' => 'suma_dojazdow',
        'backgroundColor' => '#82b54b',
        'borderColor' => '#52851b',
    ],
] as $v)
    var wykresRobocizn = new Chart(document.getElementById('{{ $v['el'] }}').getContext('2d'), {
        type: 'line',
        data: {
            labels: [
                @for ($i = $now->month; $i < $now->month+12; $i++)
                    @php
                        $month_id = $i % 12;
                        $month_id = ($month_id == 0) ? 12 : $month_id;
                    @endphp
                    '{{ $months->where('id', $month_id)->first()->short_name }}',
                @endfor
            ],
            datasets: [{
                label: '{{ $v['name'] }}',
                data: [
                    @php
                        $counter = 0;
                    @endphp
                    @for ($i = $now->month; $i < $now->month+12; $i++)
                        @php
                            $counter++;
                            $year_id = $now->year;
                            $month_id = $i % 12;
                            $month_id = ($month_id == 0) ? 12 : $month_id;
                            if ($month_id > $counter) {
                                $year_id--;
                            }
                        @endphp
                        '{{ @$rozliczenia->where('month', $month_id)->first()[$v['var']] ?? 0 }}',
                    @endfor
                ],
                borderWidth: 1,
                backgroundColor: '{{ $v['backgroundColor'] }}',
                borderColor: '{{ $v['borderColor'] }}',
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
@endforeach

})</script>@endsection
