@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="content">
        <zdjecia _token=@json(csrf_token()) :zlecenie_id=@json($zlecenie->id) :required_photos='@json($zlecenie->required_photos)'></zdjecia>
    </div>
@endsection
