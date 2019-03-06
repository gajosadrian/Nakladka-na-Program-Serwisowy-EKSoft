@extends('global.app')

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Rozliczenie (( $rozliczenie->nr ))</h1>
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

        <b-block>
            <template slot="content">
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
						<thead>
							<tr class="thead-light">
                                <th>#</th>
								<th>Nr zlecenia</th>
								<th>Urządzenie</th>
								<th>Status</th>
								<th>Błędy</th>
								<th>Ostatnia data</th>
								<th class="d-none"></th>
							</tr>
						</thead>
						<tbody>
                            @foreach ($zlecenia_nierozliczone as $index => $zlecenie)
                                <tr>
                                    <td>
                                        <b-form-checkbox
                                            id="checkbox{{ $zlecenie->id }}"
                                            value="accepted"
                                            unchecked-value="not_accepted">
                                        </b-form-checkbox>
                                    </td>
                                    <td class="align-middle font-w600">
                                        <a href="javascript:void(0)" onclick="PopupCenter('{{ route('zlecenia.show', $zlecenie->id) }}', 'zlecenie{{ $zlecenie->id }}', 1500, 700)">
                                            <i class="{{ $zlecenie->znacznik->icon }} mr-2"></i>
                                            {{ $zlecenie->nr_or_obcy }}
                                        </a>
                                        <a href="javascript:void(0)" class="ml-2" v-clipboard:copy="'{{ $zlecenie->nr }}'">
                                            <i class="far fa-copy"></i>
                                        </a>
                                    </td>
                                    <td>{{ $zlecenie->data_zakonczenia }}</td>
                                    <td>{{ $zlecenie->data_zakonczenia_formatted }}</td>
                                    <td class="{{ $zlecenie->status->color ? 'table-' . $zlecenie->status->color : '' }}">
                                        <i class="{{ $zlecenie->status->icon }} {{ $zlecenie->status->color ? 'text-' . $zlecenie->status->color : '' }} mx-2"></i>
                                        {{ $zlecenie->status->nazwa }}
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
