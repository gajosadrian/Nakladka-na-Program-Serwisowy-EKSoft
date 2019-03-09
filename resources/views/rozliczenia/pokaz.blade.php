@extends('global.app')

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rozliczenie nr <span class="font-w600">{{ $rozliczenie->nr }}</span></h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block>
            <template slot="content">
                <b-form-group
                    id="exampleInputGroup1"
                    label="Email address:"
                    label-for="exampleInput1"
                    description="We'll never share your email with anyone else."
                >
                    <b-form-input
                        id="exampleInput1"
                        type="email"
                        required
                        placeholder="Enter email" />
                </b-form-group>
            </template>
        </b-block>

        <b-block full>
            <template slot="content">
                Do rozliczenia: {{ $zlecenia_amount }} zleceń
            </template>
        </b-block>

        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-borderless table-vcenter font-size-sm js-table-checkable dataTable">
						<thead>
							<tr class="text-uppercase">
                                <th class="font-w700">
                                    <b-form-checkbox id="check-all" name="check-all"></b-form-checkbox>
                                </th>
                                <th class="font-w700">Nr zlecenia</th>
								<th class="font-w700">Zleceniodawca</th>
                                <th class="font-w700">Robocizny</th>
								<th class="font-w700">Dojazdy</th>
								<th class="font-w700">Przyjęcie</th>
								<th class="font-w700">Zakończenie</th>
								<th class="font-w700">Status</th>
								<th class="d-none"></th>
							</tr>
						</thead>
						<tbody>
                            @php $counter = 0 @endphp
                            @foreach ($zlecenia_nierozliczone as $zlecenie)
                                @php
                                    $counter++;
                                    $robocizny = $zlecenie->robocizny;
                                @endphp
                                <tr class="{{ ($zlecenie->is_data_zakonczenia and $zlecenie->data->gt($rozliczenie->data) or (!$zlecenie->is_data_zakonczenia and $zlecenie->data_przyjecia->gt($rozliczenie->data))) ? 'table-secondary' : '' }}">
                                    <td>
                                        <b-form-checkbox id="row_{{ $counter }}" name="row_{{ $counter }}"></b-form-checkbox>
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
                                    <td>{{ $zlecenie->zleceniodawca }}</td>
                                    <td class="{{ empty($robocizny) ? 'table-danger' : '' }}">{!! $robocizny ? $zlecenie->robocizny_html : '<span class="text-danger font-w700">Do uzupełnienia</span>' !!}</td>
                                    <td>{!! $zlecenie->dojazdy_html !!}</td>
                                    <td>{{ $zlecenie->data_przyjecia->toDateString() }}</td>
                                    <td>{!! $zlecenie->is_data_zakonczenia ? $zlecenie->data_zakonczenia->toDateString() : '<span class="text-danger font-w700">Brak terminu</span>' !!}</td>
                                    {!! $zlecenie->tableCellStatusHTML !!}
									<td class="d-none">
										{{ $zlecenie->nr }} ; {{ $zlecenie->nr_obcy }}
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
