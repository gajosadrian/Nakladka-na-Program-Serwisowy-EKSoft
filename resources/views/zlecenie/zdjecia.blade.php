@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="content">
        @include('zlecenie-zdjecie.component.index', compact('zlecenie'))
    </div>
@endsection

@section('js_after')<script>

$(document).keydown(function (e) {
	if (e.keyCode == 27) {
		window.close();
	}
});

history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};

</script>@append
