{{--
    Sidebar Mini Mode - Display Helper classes

    Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
    Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
        If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

    Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
    Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
    Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
--}}
<nav id="sidebar" aria-label="Main Navigation">
    {{-- Side Header --}}
    {{-- <div class="bg-header-dark">
        <div class="content-header bg-white-10">
            <a class="link-fx font-w600 font-size-lg text-white" href="{{ route('home') }}">
                <span class="smini-visible">
                    <span class="text-white-75">S</span><span class="text-white">S</span>
                </span>
                <span class="smini-hidden">
                    <span class="text-white-75">SE</span><span class="text-white">R</span><span class="text-white-75">WIS</span>
                </span>
            </a>

            <div>
                <a class="d-lg-none text-white ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                    <i class="fa fa-times-circle"></i>
                </a>
            </div>
        </div>
    </div> --}}
    {{-- END Side Header --}}

    {{-- Side Navigation --}}
    <div class="content-side content-side-full">
        @include('global.nav')
    </div>
    {{-- END Side Navigation --}}
</nav>
