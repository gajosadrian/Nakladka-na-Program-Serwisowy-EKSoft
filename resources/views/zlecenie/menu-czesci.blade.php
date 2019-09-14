@extends('global.app', [ 'window' => true ])
@php
    $items = [
        [
            'title' => 'Szykowanie części',
            'desc' => 'dla serwisanta na kolejny wyjazd',
            'icon' => 'fa fa-2x fa-briefcase',
            'style' => 'xplay',
            'url' => route('zlecenia.szykowanieCzesci'),
        ],
        [
            'title' => 'Odbiór części',
            'desc' => 'po wykonanych naprawach',
            'icon' => 'fa fa-2x fa-check-circle',
            'style' => 'xeco',
            'url' => route('zlecenia.odbiorCzesci'),
        ],
        [
            'title' => 'Dodawanie części',
            'desc' => 'do dzisiejszych zleceń na bieżąco',
            'icon' => 'fa fa-2x fa-plus-circle',
            'style' => 'xpro',
            'url' => route('zlecenia.dodawanieCzesci'),
        ],
    ];
@endphp

@section('content')
    <div class="content">
        <div class="row row-deck">
            @foreach ($items as $item)
                <div class="col-md-6 col-xl-3">
                    <a class="block block-rounded block-link-shadow d-flex justify-content-center align-items-start text-center bg-{{ $item['style'] }}" href="{{ $item['url'] }}">
                        <div class="block-content block-content-full bg-white mt-1 align-self-stretch">
                            <div class="py-4">
                                <i class="{{ $item['icon'] }} text-{{ $item['style'] }}"></i>
                                <p class="font-size-lg font-w600 mt-3 mb-1">
                                    {{ $item['title'] }}
                                </p>
                                <p class="text-muted mb-0">
                                    {{ $item['desc'] }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
