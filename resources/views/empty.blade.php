@extends('global.app')

@section('content')

<div class="content">
    @if (@auth()->user()->technik_id)
        <div class="row gutters-tiny">
            <div class="col-6 col-md-4 col-xl-2">
                <a class="block block-bordered block-link-shadow text-center" href="{{ route('zlecenia.mobileApp') }}">
                    <div class="block-content block-content-full aspect-ratio-4-3 d-flex justify-content-center align-items-center">
                        <div>
                            <i class="fa fa-2x fa-mobile-alt text-primary"></i>
                            <div class="font-w600 mt-3 text-uppercase">Aplikacja</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif
</div>

{{-- <div class="content">
    <div class="bg-success" style="width:850px; height: 480px; overflow: hidden; transform: scale(1); transform-origin: top left;">
        <div id="map" style="position: relative; left: 0px; top: 0px; z-index: 1; overflow: visible;">
            @for ($y = 0; $y <= 9; $y++)
                @for ($x = 0; $x <= 9; $x++)
                    <span class="text-white px-1" onmouseover="if (window.mouseDown) $(this).addClass('bg-danger')">x</span>
                @endfor
                <br>
            @endfor
        </div>
    </div>
</div>

<script>window.onload = function() {

var roomSpeed = 1;
var camera = {};
camera.directions = Array(4).fill(false);
camera.speed = 1;
window.mouseDown = false;
var map = $('#map');

setInterval(function() {
    let x = parseInt(map.css('left'));
    let y = parseInt(map.css('top'));

    // 0 up, 1 right, 2 down, 3 left
    if (camera.directions[0])
        map.css('top', y + camera.speed);
    if (camera.directions[1])
        map.css('left', x - camera.speed);
    if (camera.directions[2])
        map.css('top', y - camera.speed);
    if (camera.directions[3])
        map.css('left', x + camera.speed);
}, roomSpeed);

window.addEventListener('keydown', function(e){
    switch (e.keyCode) {
        case 87:
        case 38:
            camera.directions[0] = true;
        break

        case 68:
        case 39:
            camera.directions[1] = true;
        break

        case 83:
        case 40:
            camera.directions[2] = true;
        break

        case 65:
        case 37:
            camera.directions[3] = true;
        break
    }
});
window.addEventListener('keyup', function(e){
    switch (e.keyCode) {
        case 87:
        case 38:
            camera.directions[0] = false;
        break

        case 68:
        case 39:
            camera.directions[1] = false;
        break

        case 83:
        case 40:
            camera.directions[2] = false;
        break

        case 65:
        case 37:
            camera.directions[3] = false;
        break
    }
});
window.addEventListener('selectstart', function(e) {
    e.preventDefault()
});
document.addEventListener('mousedown', function(e) {
    window.mouseDown = true;
});
document.addEventListener('mouseup', function(e) {
    window.mouseDown = false;
});

}</script> --}}

@endsection
