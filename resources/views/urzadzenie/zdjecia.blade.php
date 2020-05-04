@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Zdjęcia urządzeń</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <urzadzenia-zdjecia _token=@json(csrf_token()) />
    </div>
@endsection
