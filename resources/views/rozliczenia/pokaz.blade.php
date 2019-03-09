@extends('global.app')
@php
    $room = rand();
@endphp

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
                <div class="">
                    <b-button variant="primary" size="sm" onclick="rozliczZaznaczone{{ $room }}()">Rozlicz zaznaczone</b-button>
                </div>
                <div class="table-responsive">
                    <table id="{{ $room }}" class="table table-striped table-hover table-borderless table-vcenter font-size-sm js-table-checkable dataTable">
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
                                <tr data-zlecenie_id="{{ $zlecenie->id }}" class="{{ ($zlecenie->is_data_zakonczenia and $zlecenie->data->gt($rozliczenie->data) or (!$zlecenie->is_data_zakonczenia and $zlecenie->data_przyjecia->gt($rozliczenie->data))) ? 'table-secondary' : '' }}">
                                    <td>
                                        <b-form-checkbox id="row_{{ $counter }}" name="row_{{ $counter }}"></b-form-checkbox>
                                    </td>
                                    {!! $zlecenie->tableCellNrHTML !!}
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

@section('js_after')<script>
    function rozliczZaznaczone{{ $room }} () {
        let zlecenia_ids = [];

        $('table#{{ $room }} tbody tr').each(function () {
            let $row = $(this);

            if ($row.find('input[type=checkbox]:checked').length) {
                let zlecenie_id = String($row.data('zlecenie_id'));

                zlecenia_ids.push(zlecenie_id);
            }
        });

        $.post('{{ route('rozliczone_zlecenia.storeMany') }}', { '_token': '{{ csrf_token() }}', rozliczenie_id: {{ $rozliczenie->id }}, zlecenia_ids: zlecenia_ids })
            .done(function( data ) {
                console.log(data);
            });
    }
</script>@endsection
