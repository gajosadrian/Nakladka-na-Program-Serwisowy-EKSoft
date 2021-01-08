@extends('global.app', ['nofooter' => true])
@php
  $user = auth()->user();
@endphp

@section('content')
  <div class="content py-0">
    <zlecenie-lista _token="{{ csrf_token() }}"></zlecenie-lista>
  </div>
@endsection
