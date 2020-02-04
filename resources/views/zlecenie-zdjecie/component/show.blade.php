@php
    $room = str_random();
@endphp

<div class="push">
    <h4 class="mb-0">{{ $title }}</h4>
    <hr class="mt-1">

    <b-row>
        <b-col cols="12">
            @csrf
            <input type="hidden" name="zlecenie_id" id="zlecenie_id{{ $room }}" value="{{ $zlecenie->id }}">
            <input type="hidden" name="urzadzenie_id" id="urzadzenie_id{{ $room }}" value="{{ $zlecenie->urzadzenie_id }}">
            <input type="hidden" name="save_to" id="save_to{{ $room }}" value="{{ $save_to }}">
            <input type="hidden" name="type" id="type{{ $room }}" value="{{ $type }}">
            <input type="file" name="image" id="image{{ $room }}" accept="image/*" onchange="onImageChange{{ $room }}(this)">
        </b-col>
    </b-row>

    <b-row class="gutters-tiny js-gallery img-fluid-100">
        @foreach ($zlecenie->zdjecia->where('type', $type) as $zdjecie)
            <div class="col-4 col-md-3 col-lg-2 animated ribbon ribbon-danger">
                @if ($zdjecie->is_deletable)
                    <div class="ribbon-box">
                        <form action="{{ route('zlecenie-zdjecie.destroy', $zdjecie->id) }}" method="post">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-white">
                                <i class="fa fa-times"></i>
                            </button>
                        </form>
                    </div>
                @endif
                <a class="img-link img-link-zoom-in img-thumb img-lightbox" href="{{ $zdjecie->url }}">
                    <img class="img-fluid" src="{{ $zdjecie->url }}">
                </a>
            </div>
        @endforeach
    </b-row>
</div>

@section('js_after')<script>

const imageCompressor{{ $room }} = new ImageCompressor();
const formData{{ $room }} = new FormData();

formData{{ $room }}.append('_token', $('input[name="_token"]').val() );
formData{{ $room }}.append('zlecenie_id', $('#zlecenie_id{{ $room }}').val() );
formData{{ $room }}.append('urzadzenie_id', $('#urzadzenie_id{{ $room }}').val() );
formData{{ $room }}.append('save_to', $('#save_to{{ $room }}').val() );
formData{{ $room }}.append('type', $('#type{{ $room }}').val() );

function onImageChange{{ $room }}(self) {
    let file = self.files[0];
    if ( ! file) return;

    swal({
        position: 'center',
        type: 'info',
        title: 'Wysyłanie...',
        showConfirmButton: false,
        allowOutsideClick: false
    });

    imageCompressor{{ $room }}.compress(file, {
        maxWidth: 850,
        maxHeight: 850,
        quality: 1.0,
    })
        .then((result) => {
            formData{{ $room }}.append('image', result, result.name);

            axios.post( route('zlecenie-zdjecie.store') , formData{{ $room }}).then((data) => {
                data = data.data;
                console.log(data);
                location.reload();
            });
        })
        .catch((err) => {
            console.log(err);
        })
}

</script>@append
