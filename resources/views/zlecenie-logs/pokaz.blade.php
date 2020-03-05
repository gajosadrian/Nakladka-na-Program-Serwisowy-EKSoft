@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zdarzenia</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <div class="mb-3">
                    @foreach ($technicy as $_technik)
                        <b-link href="{{ route('zlecenia.logs', [ 'technik_id' => $_technik->id, 'date_string' => $date_string ]) }}" class="btn btn-outline-primary {{ ($_technik->id == @$technik->id) ? 'active' : '' }}">{{ $_technik->nazwa }}</b-link>
                    @endforeach
                </div>

                <b-row>
                    @if ($technik)
                        <b-col cols="2">
                            <input type="text" class="js-datepicker form-control" value="{{ $date_string }}" onchange="updateUrl(this, 'date_string')">
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
            <b-block title="Zdarzenia">
                <template slot="content">
                    <table class="table">
                        <tbody>
                            @foreach ($grouped_logs as $logs)
                                <tr>
                                    <th class="table-active" colspan="2" onclick="{{ $logs[0]->zlecenie->popup_link }}" style="cursor: pointer;">
                                        {{ $logs[0]->zlecenie->nr }}
                                    </th>
                                </tr>
                                @foreach ($logs as $log)
                                    <tr>
                                        @if ($log->is_opis)
                                            <td class="table-info" style="width:1%;" nowrap>
                                                <i class="fa fa-edit text-info mr-2"></i>
                                                Dodanie opisu
                                            </td>
                                            <td>{{ $log->opis }}</td>
                                        @elseif ($log->is_status)
                                            <td class="table-{{ $log->status->color }}" style="width:1%;" nowrap>
                                                <i class="{{ $log->status->icon }} text-{{ $log->status->color }} mr-2"></i>
                                                Zmiana statusu
                                            </td>
                                            <td>{{ $log->status->nazwa }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

let technik_id = @json(@$technik->id ?? 0);
let date_string = @json($date_string);

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('zlecenia.logs', {
        technik_id: (type == 'technik_id') && value || technik_id,
        date_string: (type == 'date_string') && value || date_string,
    }));
}

</script>@endsection
