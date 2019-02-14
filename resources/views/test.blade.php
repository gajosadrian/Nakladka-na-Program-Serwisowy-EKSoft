@extends('layout')

@section('content')
    {{ date('Y-m-d') }}
    <ul>
        @foreach ($zlecenia as $key => $zlecenie)
            <li>{{ $zlecenie->nr }} - {{ $zlecenie->data_przyjecia_formatted }} - {{ $zlecenie->dni_od_przyjecia }} - {{ $zlecenie->data_zakonczenia_formatted }} - {{ $zlecenie->dni_od_zakonczenia }} - {{ $zlecenie->status->nazwa }} @ {{ $zlecenie->error }}</li>
            {{-- <ul>
                <li>{!! $zlecenie->opisBr !!}</li>
            </ul> --}}
        @endforeach
    </ul>
@endsection
