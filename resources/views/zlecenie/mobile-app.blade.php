@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="content">
        <zlecenie-mobile-app _token=@json(csrf_token()) />
    </div>
@endsection
