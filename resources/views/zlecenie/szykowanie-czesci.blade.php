@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Szykowanie części</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry" full>
            <template slot="content">
                <b-row>
                    <b-col>
                        <select class="form-control" onchange="updateUrl(this, 'technik_id')">
                            <option value="0">-- Technik --</option>
                            @foreach ($technicy as $_technik)
                                <option value="{{ $_technik->id }}" {{ ($_technik->id == @$technik->id) ? 'selected' : '' }}>
                                    {{ $_technik->nazwa }}
                                </option>
                            @endforeach
                        </select>
                    </b-col>
                    <b-col>
                        <input type="text" class="js-datepicker form-control" value="{{ $date_string }}" onchange="updateUrl(this, 'date_string')">
                    </b-col>
                </b-row>
            </template>
        </b-block>
    </div>
@endsection

@section('js_after')<script>

let technik_id = @json(@$technik->id ?? 0);
let date_string = @json($date_string);

function updateUrl(_this, type) {
    let value = $(_this).val();

    window.location.replace(route('zlecenia.szykowanieCzesci', {
        technik_id: (type == 'technik_id') && value || technik_id,
        date_string: (type == 'date_string') && value || date_string,
    }));
}

</script>@endsection
