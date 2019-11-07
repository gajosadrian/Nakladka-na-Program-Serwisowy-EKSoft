@extends('global.app', [ 'window' => true ])

@php
    $user = auth()->user();
@endphp

@section('content')
    <div class="content">
        @include('zlecenie-zdjecie.component.show', [
            'title' => 'Tabliczka',
            'type' => App\Models\Zlecenie\Zdjecie::TYPE_TABLICZKA,
            'zlecenie' => $zlecenie,
        ])
        @if ($zlecenie->is_gwarancja)
            @include('zlecenie-zdjecie.component.show', [
                'title' => 'Gwarancja',
                'type' => App\Models\Zlecenie\Zdjecie::TYPE_GWARANCJA,
                'zlecenie' => $zlecenie,
            ])
        @endif
        @if ($zlecenie->is_ubezpieczenie)
            @include('zlecenie-zdjecie.component.show', [
                'title' => 'Polisa',
                'type' => App\Models\Zlecenie\Zdjecie::TYPE_POLISA,
                'zlecenie' => $zlecenie,
            ])
        @endif
        @if ($zlecenie->is_gwarancja or $zlecenie->is_ubezpieczenie)
            @include('zlecenie-zdjecie.component.show', [
                'title' => 'Dowód zakupu',
                'type' => App\Models\Zlecenie\Zdjecie::TYPE_DOWOD_ZAKUPU,
                'zlecenie' => $zlecenie,
            ])
            @include('zlecenie-zdjecie.component.show', [
                'title' => 'Urządzenie',
                'type' => App\Models\Zlecenie\Zdjecie::TYPE_URZADZENIE,
                'zlecenie' => $zlecenie,
            ])
        @endif
        @include('zlecenie-zdjecie.component.show', [
            'title' => 'Inne',
            'type' => null,
            'zlecenie' => $zlecenie,
        ])
    </div>
@endsection

@section('js_after')<script>

$(document).keydown(function (e) {
	if (e.keyCode == 27) {
		window.close();
	}
});

</script>@endsection
