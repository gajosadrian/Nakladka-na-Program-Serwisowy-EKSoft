<div class="push">
    <h4 class="mb-0">{{ $title }}</h4>
    <hr class="mt-1">

    <b-row>
        <b-col cols="12">
            <form action="{{ route('zlecenie-zdjecie.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="zlecenie_id" value="{{ $zlecenie->id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="file" name="image" accept="image/*">

                <button type="submit" name="button">Submit</button>
            </form>
        </b-col>
    </b-row>

    <b-row class="gutters-tiny js-gallery img-fluid-100">
        @foreach ($zlecenie->zdjecia->where('type', $type) as $zdjecie)
            <div class="col-4 col-md-3 col-lg-2 animated">
                <a class="img-link img-link-zoom-in img-thumb img-lightbox" href="{{ $zdjecie->url }}">
                    <img class="img-fluid" src="{{ $zdjecie->url }}">
                </a>
            </div>
        @endforeach
    </b-row>
</div>
