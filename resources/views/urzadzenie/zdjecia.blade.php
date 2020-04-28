@extends('global.app')

@section('content')
    <div class="content">
        <urzadzenia-zdjecia _token=@json(csrf_token()) />
    </div>
@endsection
