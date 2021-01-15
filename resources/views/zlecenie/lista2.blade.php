@extends('global.app', ['nofooter' => true])
@php
  $user = auth()->user();
@endphp

@section('content')
  <div class="content px-2 py-0">
    <zlecenie-lista
      _token="{{ csrf_token() }}"
      :statusy='@json($statusy)'
      :technicy='@json($technicy)'
      :_search='@json($search)'
      :_column-widths='@json($columnWidths)'
    ></zlecenie-lista>
  </div>
@endsection
