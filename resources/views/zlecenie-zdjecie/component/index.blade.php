@if ($zlecenie->urzadzenie_id)
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Tabliczka',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_TABLICZKA,
        'zlecenie' => $zlecenie,
    ])
@endif
@if ($zlecenie->urzadzenie_id and $zlecenie->is_gwarancja)
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Gwarancja',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_GWARANCJA,
        'zlecenie' => $zlecenie,
    ])
@endif
@if ($zlecenie->urzadzenie_id and ($zlecenie->is_gwarancja or $zlecenie->is_ubezpieczenie))
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Polisa',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_POLISA,
        'zlecenie' => $zlecenie,
    ])
@endif
@if ($zlecenie->urzadzenie_id and ($zlecenie->is_gwarancja or $zlecenie->is_ubezpieczenie))
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Dowód zakupu',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_DOWOD_ZAKUPU,
        'zlecenie' => $zlecenie,
    ])
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Urządzenie',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_URZADZENIE,
        'zlecenie' => $zlecenie,
    ])
@endif
@if ($zlecenie->urzadzenie_id)
    @include('zlecenie-zdjecie.component.show', [
        'title' => 'Urządzenie',
        'save_to' => 'urzadzenie',
        'type' => App\Models\Zlecenie\Zdjecie::TYPE_URZADZENIE,
        'zlecenie' => $zlecenie,
    ])
@endif
@include('zlecenie-zdjecie.component.show', [
    'title' => 'Inne',
    'save_to' => 'zlecenie',
    'type' => App\Models\Zlecenie\Zdjecie::TYPE_INNE,
    'zlecenie' => $zlecenie,
])
