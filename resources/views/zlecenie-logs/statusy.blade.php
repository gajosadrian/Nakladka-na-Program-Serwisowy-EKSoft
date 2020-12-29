@php
    $is_mobile = auth()->user()->is_mobile;
@endphp

@extends('global.app')

@section('content')
    <div class="bg-body-light d-print-none">
        <div class="content content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Historia statusów</h1>
            </div>
        </div>
    </div>

    <div class="content">
        <b-block title="Parametry">
            <template slot="content">
                {{-- <b-row>
                    <b-col>
                        <select class="form-control form-control-alt" onchange="updateUrl(this, 'pracownik_id')">
                            <option value="0">-- Pracownik --</option>
                            @foreach ($pracownicy as $_pracownik)
                                <option value="{{ $_pracownik->id }}" {{ ($_pracownik->id == @$pracownik->id) ? 'selected' : '' }}>
                                    {{ $_pracownik->nazwa }}
                                </option>
                            @endforeach
                        </select>
                    </b-col>
                </b-row> --}}
                <div class="mb-3">
                    @foreach ($pracownicy as $_pracownik)
                        <b-button variant="outline-primary" class="{{ ($_pracownik->id == @$pracownik->id) ? 'active' : '' }}" onclick="updateUrl({{ $_pracownik->id }}, 'pracownik_id')">
                            {{ $_pracownik->nazwa }}
                        </b-button>
                    @endforeach
                </div>
            </template>
        </b-block>

        @if ($pracownik)
            <b-block full>
                <template slot="content">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-vcenter font-size-sm">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="font-w700" style="width:1%;">Lp.</th>
                                    <th class="font-w700" style="width:1%;">Data</th>
                                    <th class="font-w700" style="width:1%;">Status</th>
                                    <th class="font-w700" style="width:1%;">Zlecenie</th>
                                    <th class="font-w700">Pracownik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statusy_data as $data => $statusy)
                                    <tr>
                                        <th></th>
                                        <th colspan="4">{{ $data }}</th>
                                    </tr>
                                    @php
                                        $counter = 0;
                                    @endphp
                                    @foreach ($statusy->sortBy('data') as $status)
                                        @php
                                            $counter++;
                                        @endphp
                                        <tr>
                                            <th>{{ $counter }}</th>
                                            <td nowrap>{{ $status->data_formatted }}</td>
                                            <td class="table-{{ $status->status->color }}" nowrap>
                                                <i class="{{ $status->status->icon }} text-{{ $status->status->color }}"></i>
                                                {{ $status->nazwa }}
                                            </td>
                                            <td onclick="{{ $status->zlecenie ? $status->zlecenie->popup_link : '' }}" style="cursor:pointer;" nowrap>
                                                {{ $status->zlecenie ? $status->zlecenie->nr : '' }}
                                            </td>
                                            <td nowrap>{{ $status->pracownik->nazwa }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </template>
            </b-block>
        @endif
    </div>
@endsection

@section('js_after')<script>

const pracownik_id = @json(@$pracownik->id ?? 0)

function updateUrl(self, type) {
    let value;
    if (Number.isInteger(self)) {
        value = self;
    } else {
        value = $(self).val()
    }

    location.replace(route('zlecenia.logs.statusy', {
        pracownik_id: (type == 'pracownik_id') && value || pracownik_id,
    }))
}

</script>@append
