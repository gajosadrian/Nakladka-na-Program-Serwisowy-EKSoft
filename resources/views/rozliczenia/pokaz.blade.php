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
        <b-block full>
            <template slot="content">
                <b-row class="text-center">
                    @foreach ([
                        [true, 'fa fa-user-check text-primary', false, $rozliczenie->rozliczyl, ($rozliczenie->is_closed ? 'Rozliczył' : 'Utworzył')],
                        [!$rozliczenie->is_closed, 'fa fa-clock text-primary', 'zlecenia_nierozliczone_amount' . $room, $zlecenia_nierozliczone_amount, 'Zlecenia nierozliczone'],
                        [true, 'fa fa-check text-success', 'rozliczone_zlecenia_amount' . $room, $rozliczone_zlecenia_amount, 'Zlecenia rozliczone']
                    ] as $value)
                        @if ($value[0])
                            <b-col cols="2">
                                <div class="py-3 border-right">
                                    <div class="item item-circle bg-body-light mx-auto">
                                        <i class="{{ $value[1] }}"></i>
                                    </div>
                                    <p class="font-size-h3 font-w300 mt-3 mb-0">
                                        <span id="{{ $value[2] }}">{{ $value[3] }}</span>
                                    </p>
                                    <p class="text-muted mb-0">
                                        {{ $value[4] }}
                                    </p>
                                </div>
                            </b-col>
                        @endif
                    @endforeach
                </b-row>

                {{-- <b-form-group
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
                </b-form-group> --}}
            </template>
        </b-block>

        <div id="accordion" role="tablist" aria-multiselectable="true">
            <div class="block block-rounded shadow-sm mb-1">
                <div class="block-header block-header-default" role="tab" id="accordion_h1">
                    <a class="font-w600" data-toggle="collapse" data-parent="#accordion" href="#accordion_q1" aria-expanded="true" aria-controls="accordion_q1">
                        Rozliczone zlecenia
                    </a>
                </div>
                <div id="accordion_q1" class="collapse {{ $rozliczenie->is_closed ? 'show' : '' }}" role="tabpanel" aria-labelledby="accordion_h1" data-parent="#accordion">
                    <div class="block-content">
                        <div class="table-responsive">
                            <table id="rozliczone{{ $room }}" class="table table-striped table-hover table-borderless table-vcenter font-size-sm dataTable">
                                <thead>
                                    <th class="font-w700">Nr zlecenia</th>
                                    <th class="font-w700">Zleceniodawca</th>
                                    <th class="font-w700">Robocizny</th>
                                    <th class="font-w700">Dojazdy</th>
                                    <th class="d-none"></th>
                                </thead>
                                <tbody>
                                    @foreach ($rozliczone_zlecenia as $rozliczone_zlecenie)
                                        <tr>
                                            {!! $rozliczone_zlecenie->zlecenie->tableCellNrHTML !!}
                                            <td>{{ $rozliczone_zlecenie->zleceniodawca }}</td>
                                            <td>{!! $rozliczone_zlecenie->robocizny_html !!}</td>
                                            <td>{!! $rozliczone_zlecenie->dojazdy_html !!}</td>
                                            <td class="d-none">
                                                {{ $rozliczone_zlecenie->zlecenie->nr }} ; {{ $rozliczone_zlecenie->zlecenie->nr_obcy }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if (! $rozliczenie->is_closed)
                <div class="block block-rounded shadow-sm mb-1">
                    <div class="block-header block-header-default" role="tab" id="accordion_h2">
                        <a class="font-w600" data-toggle="collapse" data-parent="#accordion" href="#accordion_q2" aria-expanded="true" aria-controls="accordion_q2">
                            Nierozliczone zlecenia
                        </a>
                    </div>
                    <div id="accordion_q2" class="collapse show" role="tabpanel" aria-labelledby="accordion_h2" data-parent="#accordion">
                        <div class="block-content">
                            <div>
                                <b-button variant="primary" size="sm" onclick="rozliczZaznaczone{{ $room }}()">Rozlicz zaznaczone</b-button>
                            </div>
                            <div class="table-responsive">
                                <table id="nierozliczone{{ $room }}" class="table table-striped table-hover table-borderless table-vcenter font-size-sm js-table-checkable dataTable">
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
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js_after')<script>
    function rozliczZaznaczone{{ $room }} () {
        let zlecenia_ids = [];
        let rows_refs = [];

        $('table#nierozliczone{{ $room }} tbody tr').each(function () {
            let $row = $(this);

            if ($row.find('input[type=checkbox]:checked:enabled').length) {
                let zlecenie_id = String($row.data('zlecenie_id'));

                zlecenia_ids.push(zlecenie_id);
                rows_refs.push($row);
            }
        });

        $.post('{{ route('rozliczone_zlecenia.storeMany') }}', { '_token': '{{ csrf_token() }}', rozliczenie_id: {{ $rozliczenie->id }}, zlecenia_ids: zlecenia_ids })
            .done(function( data ) {
                $.each( rows_refs, function( index, $row ) {
                    $row.find('input[type=checkbox]:checked').prop('checked', false).prop('disabled', true);
                    $row.addClass('d-none');
                });

                let $zlecenia_nierozliczone = $('#zlecenia_nierozliczone_amount{{ $room }}');
                let $rozliczone_zlecenia = $('#rozliczone_zlecenia_amount{{ $room }}');
                let zlecenia_nierozliczone_amount = Number($zlecenia_nierozliczone.text());
                let rozliczone_zlecenia_amount = Number($rozliczone_zlecenia.text());

                $zlecenia_nierozliczone.text(zlecenia_nierozliczone_amount - zlecenia_ids.length);
                $rozliczone_zlecenia.text(rozliczone_zlecenia_amount + zlecenia_ids.length);
            });
    }
</script>@endsection
