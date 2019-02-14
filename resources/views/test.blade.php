@extends('layout')

@section('content')
    {{ date('Y-m-d') }}
    <ul>
        @foreach ($zlecenia as $key => $zlecenie)
            <li>{{ $zlecenie->nr }} - {{ $zlecenie->data_przyjecia }} - {{ $zlecenie->dni_od_przyjecia }} - {{ $zlecenie->data_zakonczenia }} - {{ $zlecenie->dni_od_zakonczenia }} - {{ $zlecenie->status->nazwa }}</li>
            {{-- <ul>
                <li>{!! $zlecenie->opisBr !!}</li>
            </ul> --}}
        @endforeach
    </ul>
@endsection
