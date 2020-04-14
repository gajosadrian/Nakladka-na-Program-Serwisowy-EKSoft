@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="content">
        <Zdjecia _token=@json(csrf_token()) :zlecenie_id=@json($zlecenie->id) />
    </div>
@endsection
