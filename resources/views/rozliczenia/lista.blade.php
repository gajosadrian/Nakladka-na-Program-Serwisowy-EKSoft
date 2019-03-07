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
        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-vcenter font-size-sm">
						<thead>
							<tr class="text-uppercase">
                                <th class="font-w700">Okres</th>
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
                                        <b-form action="{{ route('admin.rozliczenia.store') }}" method="post">
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
                                    <td>{{ $rozliczenie->nr }}</td>
                                    <td class="text-right text-muted">-</td>
                                    <td class="text-right text-muted">-</td>
                                    <td class="text-muted">{!! $rozliczenie->is_closed ? $rozliczenie->closed_at : '<i class="text-success font-w600">w trakcie rozliczania</i>' !!}</td>
                                    <td class="text-muted">-</td>
                                    <td>
                                        <a href="{{ route('admin.rozliczenia.pokaz', [ 'id' => $rozliczenie->id ]) }}" class="font-w600" onclick="$('#storeRozliczenie').click()">
                                            <i class="far fa-eye"></i> Zobacz
                                        </a>
                                        <a href="javascript:void(0)" class="text-danger font-w600 ml-3" onclick="$('#storeRozliczenie').click()">
                                            <i class="fa fa-lock"></i> Zakończ rozliczenie
                                        </a>
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
