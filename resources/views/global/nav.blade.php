@php
    $user = auth()->user();
    $routeName = Route::currentRouteName();
    $nav = [
        [
            'name' => 'Zlecenia',
        ],
        [
            'name' => 'Lista zleceń',
            'icon' => 'si si-list',
            'route' => 'zlecenia.lista',
            'routeOptions' => [],
        ],
        [
            'name' => 'Aplikacja mobilna',
            'icon' => 'si si-screen-smartphone',
            'route' => 'zlecenia.mobileApp',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and $user->technik_id;
            },
        ],
        [
            'name' => 'Zlecenia dla technika',
            'icon' => 'si si-docs',
            'route' => 'zlecenia.dla-technika',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        // [
        //     'name' => 'Planowanie trasy',
        //     'icon' => 'si si-map',
        //     'route' => 'zlecenia.planowanieTrasy',
        //     'routeOptions' => [],
        //     'role' => 'super-admin',
        //     'if' => function() use ($user) {
        //         return $user and !$user->technik_id;
        //     },
        // ],
        [
            'name' => 'Wyszukiwanie zleceń',
            'icon' => 'si si-magnifier',
            'route' => 'zlecenia.wyszukiwanieZlecenia',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Zdarzenia',
            'icon' => 'si si-flag',
            'route' => 'zlecenia.logs',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'SMS',
        ],
        [
            'name' => 'Nowy SMS',
            'icon' => 'si si-paper-plane',
            'route' => 'sms.create',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        // [
        //     'name' => 'Historia',
        //     'icon' => 'si si-bubbles',
        //     'route' => 'sms.history',
        //     'routeOptions' => [],
        //     'if' => function() use ($user) {
        //         return $user and !$user->technik_id;
        //     },
        // ],
        // [
        //     'name' => 'Magazyn',
        //     'role' => 'super-admin',
        // ],
        // [
        //     'name' => 'Inwentaryzacja',
        //     'icon' => 'si si-flag',
        //     'route' => 'inwentaryzacja.show',
        //     'routeOptions' => [],
        //     'role' => 'super-admin',
        // ],
        [
            'name' => 'Urządzenia',
        ],
        [
            'name' => 'Zdjęcia',
            'icon' => 'si si-camera',
            'route' => 'urzadzenie.zdjecia',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Części',
        ],
        [
            'name' => 'Szykowanie części',
            'icon' => 'si si-briefcase',
            'route' => 'czesci.indexSzykowanie',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Odbiór części',
            'icon' => 'si si-check',
            'route' => 'czesci.indexOdbior',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Wyszukiwanie części',
            'icon' => 'si si-magnifier',
            'route' => 'zlecenia.wyszukiwanieCzesci',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        // [
        //     'name' => 'Dodawanie części',
        //     'icon' => 'si si-plus',
        //     'route' => 'czesci.indexDodawanie',
        //     'routeOptions' => [],
        //     'if' => function() use ($user) {
        //         return $user and !$user->technik_id;
        //     },
        // ],
        [
            'name' => 'Rozliczenia',
        ],
        [
            'name' => 'Zlecenia',
            'icon' => 'si si-bar-chart',
            'route' => 'rozliczenia.lista',
            'routeOptions' => [],
            'role' => 'super-admin',
        ],
        [
            'name' => 'Kilometrówka',
            'icon' => 'si si-disc',
            'route' => 'zlecenia.kilometrowka',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        // [
        //     'name' => 'Użytkownicy',
        //     'icon' => 'si si-users',
        //     'route' => 'admin.users.lista',
        //     'routeOptions' => [],
        //     'role' => 'super-admin',
        // ],
    ];
@endphp

<ul class="nav-main">
    @foreach ($nav as $item)
        @if ((@!$item['role'] or ($user and $user->hasAnyRole($item['role']))) and (@!$item['permission'] or ($user and $user->isPerm($item['permission']))) and (!isset($item['if']) or $item['if']()))
            @if (@$item['route'])
                <li class="nav-main-item">
                    <a class="nav-main-link {{ $routeName == $item['route'] ? 'active' : '' }}" href="{{ route($item['route'], $item['routeOptions']) ?? [] }}">
                        <i class="nav-main-link-icon {{ $item['icon'] }}"></i>
                        <span class="nav-main-link-name">{{ $item['name'] }}</span>
                        @isset($item['badge'])
                            <span class="nav-main-link-badge badge badge-pill badge-{{ $item['badgeColor'] ?? 'success' }}">{{ $item['badge'] }}</span>
                        @endisset
                    </a>
                </li>
            @elseif (@$item['subitems'])
                @php
                    $routes = [];
                    foreach ($item['subitems'] as $subitem) {
                        $routes[] = $subitem['route'];
                    }
                @endphp
                <li class="nav-main-item {{ in_array($routeName, $routes) ? 'open' : '' }}">
                    <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
                        <i class="nav-main-link-icon {{ $item['icon'] }}"></i>
                        <span class="nav-main-link-name">{{ $item['name'] }}</span>
                        @isset($item['badge'])
                            <span class="nav-main-link-badge badge badge-pill badge-{{ $item['badgeColor'] ?? 'success' }}">{{ $item['badge'] }}</span>
                        @endisset
                    </a>
                    <ul class="nav-main-submenu">
                        @foreach ($item['subitems'] as $subitem)
                            @if ((@!$subitem['role'] or ($user and $user->hasAnyRole($subitem['role']))) and (@!$subitem['permission'] or ($user and $user->isPerm($subitem['permission']))) and (!isset($subitem['if']) or $subitem['if']()))
                                <li class="nav-main-item">
                                    <a class="nav-main-link {{ $routeName == $subitem['route'] ? 'active' : '' }}" href="{{ route($subitem['route'], $subitem['routeOptions']) ?? [] }}">
                                        @if (@$subitem['icon'])
                                            <i class="nav-main-link-icon {{ $subitem['icon'] }}"></i>
                                        @endif
                                        <span class="nav-main-link-name">{{ $subitem['name'] }}</span>
                                        @isset($subitem['badge'])
                                            <span class="nav-main-link-badge badge badge-pill badge-{{ $subitem['badgeColor'] ?? 'success' }}">{{ $subitem['badge'] }}</span>
                                        @endisset
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @else
                <li class="nav-main-heading">{{ $item['name'] }}</li>
            @endif
        @endif
    @endforeach
</ul>
