@extends('global.app')

@section('content')
    <div class="bg-body-light">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Main Title</h1>
            </div>
       </div>
    </div>

    <div class="content">
        <b-block title="Zlecenia">
            <template slot="content">
                <table class="table table-sm table-striped table-hover dataTable js-dataTable-full">
                    <tr class="thead-light">
                        <th>Imię i nazwisko</th>
                        <th>Nr zlecenia</th>
                        <th>Urządzenie</th>
                        <th>Status</th>
                        <th>Błędy</th>
                        <th>Dni od zakończenia</th>
                    </tr>
                    @foreach ($zlecenia as $key => $zlecenie)
                        <tr>
                            <td><br><small class="text-muted">({{ $zlecenie->klient_id }})</small></td>
                            <td><a href="#!" class="font-w600">(A/B) {{ $zlecenie->nr_obcy ?: $zlecenie->nr }}</a> <a href="#!" class="ml-2"><i class="far fa-copy"></i></a></td>
                            <td>{{ $zlecenie->urzadzenie->producent }}<br>{{ $zlecenie->urzadzenie->nazwa }}</td>
                            <td>(icon) {{ $zlecenie->status->nazwa }}</td>
                            <td></td>
                            <td>
                                {{ $zlecenie->data_zakonczenia->format('d-m-Y') }}<br>
                                @if ($zlecenie->dni_od_zakonczenia > 0)
                                    <small class="text-muted">
                                        @if ($zlecenie->dni_od_zakonczenia >= 2)
                                            ({{ $zlecenie->dni_od_zakonczenia }} dni temu)
                                        @else
                                            (wczoraj)
                                        @endif
                                    <small>
                                @else
                                @endif
                            </td>
                            <td class="d-none">{{ $zlecenie->nr }} ; {{ $zlecenie->nr_obcy }}</td>
                        </tr>
                    @endforeach
                </table>
            </template>
        </b-block>
    </div>
@endsection
