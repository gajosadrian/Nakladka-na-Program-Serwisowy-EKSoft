<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        {{-- <meta name="theme-color" content="#3b5998"> --}}

        <title>{{ program()->name }} - {{ program()->name }}</title>

        <meta name="description" content="{{ program()->description }}">
        <meta name="author" content="{{ author()->name }}">
        <meta name="robots" content="noindex, nofollow">

        {{-- CSRF Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Icons --}}
        <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
        <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">

        {{-- Fonts and Styles --}}
        @yield('css_before')
        <link rel="stylesheet" id="css-main" href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,400i,600,700">
        <link rel="stylesheet" id="css-theme" href="{{ asset('css/dashmix.css') }}">
        <link rel="stylesheet" id="css-theme" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" id="css-theme" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">

        {{-- Theme --}}
        <link rel="stylesheet" href="{{ asset('css/themes/xmodern.css') }}">
        @yield('css_after')

        {{-- Scripts --}}
        <script>window.Laravel = {!! json_encode(['csrfToken' => csrf_token(),]) !!};</script>
        <script src="{{ asset('js/helpers.js') }}"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        {{--
            Available classes for #page-container:

        GENERIC

            'enable-cookies'                            Remembers active color theme between pages (when set through color theme helper Template._uiHandleTheme())

        SIDEBAR & SIDE OVERLAY

            'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
            'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
            'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
            'sidebar-dark'                              Dark themed sidebar

            'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
            'side-overlay-o'                            Visible Side Overlay by default

            'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

            'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

        HEADER

            ''                                          Static Header if no class is added
            'page-header-fixed'                         Fixed Header


        Footer

            ''                                          Static Footer if no class is added
            'page-footer-fixed'                         Fixed Footer (please have in mind that the footer has a specific height when is fixed)

        HEADER STYLE

            ''                                          Classic Header style if no class is added
            'page-header-dark'                          Dark themed Header
            'page-header-glass'                         Light themed Header with transparency by default
                                                        (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
            'page-header-glass page-header-dark'         Dark themed Header with transparency by default
                                                        (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

        MAIN CONTENT LAYOUT

            ''                                          Full width Main Content if no class is added
            'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
            'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)
        --}}
        <div id="page-container" class="@empty($window) sidebar-o page-header-fixed @endempty enable-page-overlay side-scroll page-header-dark">
            @empty($window)
                @include('global.sidebar')
                @include('global.header')
            @endempty
            <main id="main-container">
                <div id="app">
                    @yield('content')
                </div>
            </main>
            @empty($window)
                @include('global.footer')
            @endempty
        </div>
        {{-- END Page Container --}}

        {{-- Dashmix Core JS --}}
        <script src="{{ asset('js/dashmix.app.js') }}"></script>
        <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

        {{-- Laravel Scaffolding JS --}}
        {{-- <script src="{{ asset('js/laravel.app.js') }}"></script> --}}

        @yield('js_after')
    </body>
</html>
