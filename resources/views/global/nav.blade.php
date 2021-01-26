@php
    $user = auth()->user();
    $routeName = Route::currentRouteName();
    $nav = [
        [
            'name' => @$user->name,
            'icon' => 'fa fa-user',
            'subitems' => [
                [
                    'name' => 'Profil',
                    'route' => 'profile.show',
                    'routeOptions' => [],
                ],
                [
                    'name' => 'Wyloguj',
                    'route' => 'logout',
                    'routeOptions' => [],
                ],
            ],
        ],
        [
            'name' => 'Zlecenia',
        ],
        [
            'name' => 'Zlecenia',
            'icon' => 'fa fa-list-ul',
            'route' => 'zlecenia.lista',
            'routeOptions' => [],
        ],
        [
            'name' => 'Zlecenia',
            'icon' => 'fa fa-list-ul',
            'route' => 'zlecenia.lista2',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and $user->id === 1;
            },
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
            'name' => 'Umawianie',
            'icon' => 'fa fa-file',
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
            'icon' => 'fa fa-search',
            'route' => 'zlecenia.wyszukiwanieZlecenia',
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
        [
            'name' => 'Dodatki',
        ],
        [
            'name' => 'Zdjęcia tabliczek',
            'icon' => 'fa fa-camera',
            'route' => 'urzadzenie.zdjecia',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'SMS',
            'icon' => 'fa fa-comment-dots',
            'route' => 'sms.create',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        // [
        //     'name' => 'Inwentaryzacja',
        //     'icon' => 'fa fa-warehouse',
        //     'route' => 'inwentaryzacja.show',
        //     'routeOptions' => [],
        // ],
        [
            'name' => 'Historia techników',
            'icon' => 'fa fa-history',
            'route' => 'zlecenia.logs',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Historia statusów',
            'icon' => 'fa fa-history',
            'route' => 'zlecenia.logs.statusy',
            'routeOptions' => [],
            'role' => 'super-admin',
        ],

        [
            'name' => 'Części',
        ],
        [
            'name' => 'Szykowanie',
            'icon' => 'fa fa-boxes',
            'route' => 'czesci.indexSzykowanie',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Odbiór',
            'icon' => 'fa fa-check',
            'badge' => \Illuminate\Support\Facades\Cache::remember('niesprawdzone_czesci_count', 30 *60, function () {
                return \App\Models\Czesc\Naszykowana::getNiesprawdzoneCount();
            }),
            'badgeColor' => 'danger',
            'route' => 'czesci.indexOdbior',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Wyszukiwanie części',
            'icon' => 'fa fa-search',
            'route' => 'zlecenia.wyszukiwanieCzesci',
            'routeOptions' => [],
            'if' => function() use ($user) {
                return $user and !$user->technik_id;
            },
        ],
        [
            'name' => 'Bez zdjęć',
            'icon' => 'fa fa-image',
            'route' => 'czesci.bezZdjec',
            'routeOptions' => [],
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
            'icon' => 'fa fa-chart-line',
            'route' => 'rozliczenia.lista',
            'routeOptions' => [],
            'role' => 'super-admin',
        ],
        [
            'name' => 'Kilometrówka',
            'icon' => 'fa fa-car-side',
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
                        @if(isset($item['badge']) and $item['badge'] > 0)
                            <span class="nav-main-link-badge badge badge-pill badge-{{ $item['badgeColor'] ?? 'success' }}">{{ $item['badge'] }}</span>
                        @endif
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
                        @if(isset($item['badge']) and $item['badge'] > 0)
                            <span class="nav-main-link-badge badge badge-pill badge-{{ $item['badgeColor'] ?? 'success' }}">{{ $item['badge'] }}</span>
                        @endif
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
