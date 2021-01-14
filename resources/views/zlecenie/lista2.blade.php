@extends('global.app', ['nofooter' => true])
@php
  $user = auth()->user();
@endphp

@section('content')
  <div class="content py-0">
    <zlecenie-lista
      _token="{{ csrf_token() }}"
      :_search='@json($search)'
      :_columnWidths='@json($columnWidths)'
    ></zlecenie-lista>
  </div>
@endsection
