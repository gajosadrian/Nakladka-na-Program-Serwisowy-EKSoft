@extends('global.app')

@section('content')
  <div class="bg-body-light d-print-none">
    <div class="content content-full">
      <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
        <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Wysyłanie SMS</h1>
      </div>
    </div>
  </div>

  <div class="content">
    <b-row>
      <b-col lg="4">
        <b-block full>
          <template slot="content">
            <sms-create _token=@json(csrf_token()) />
          </template>
        </b-block>
      </b-col>
    </b-row>
  </div>
@endsection
