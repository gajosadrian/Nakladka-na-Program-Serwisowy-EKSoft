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
            'name' => 'Zlecenia dla technika',
            'icon' => 'si si-docs',
            'route' => 'zlecenia.dla-technika',
            'routeOptions' => [],
        ],
        [
            'name' => 'Kilometrówka',
            'icon' => 'si si-disc',
            'route' => 'zlecenia.kilometrowka',
            'routeOptions' => [],
        ],
        [
            'name' => 'Wyszukiwanie części',
            'icon' => 'si si-wrench',
            'route' => 'zlecenia.wyszukiwanieCzesci',
            'routeOptions' => [],
        ],
        [
            'name' => 'Wyszukiwanie zlecenia',
            'icon' => 'si si-doc',
            'route' => 'zlecenia.wyszukiwanieZlecenia',
            'routeOptions' => [],
        ],
        // [
        //     'name' => 'Części',
        // ],
        // [
        //     'name' => 'Szykowanie części',
        //     'icon' => 'si si-briefcase',
        //     'route' => 'zlecenia.szykowanieCzesci',
        //     'routeOptions' => [],
        // ],
        // [
        //     'name' => 'Odbiór części',
        //     'icon' => 'si si-check',
        //     'route' => 'zlecenia.odbiorCzesci',
        //     'routeOptions' => [],
        // ],
        // [
        //     'name' => 'Dodawanie części',
        //     'icon' => 'si si-plus',
        //     'route' => 'zlecenia.dodawanieCzesci',
        //     'routeOptions' => [],
        // ],
        [
            'name' => 'Admin',
            'role' => 'super-admin',
        ],
        [
            'name' => 'Rozliczenia',
            'icon' => 'si si-bar-chart',
            'route' => 'rozliczenia.lista',
            'routeOptions' => [],
            'role' => 'super-admin',
        ],
        [
            'name' => 'Użytkownicy',
            'icon' => 'si si-users',
            'route' => 'admin.users.lista',
            'routeOptions' => [],
            'role' => 'super-admin',
        ],
    ];
@endphp

<ul class="nav-main">
    @foreach ($nav as $item)
        @if((@!$item['role'] or $user->hasanyrole($item['role'])) and (@!$item['permission'] or $user->hasanyrole($item['permission'])))
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
                            @if((@!$subitem['role'] or $user->hasanyrole($subitem['role'])) and (@!$subitem['permission'] or $user->hasanyrole($subitem['permission'])))
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
