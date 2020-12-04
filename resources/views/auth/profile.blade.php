@extends('global.app')

@section('content')
  <div class="bg-body-light d-print-none">
    <div class="content content-full">
      <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
        <h1 class="flex-sm-fill font-size-h2 font-w400 mt-2 mb-0 mb-sm-2">Profil {{ $user->name }}</h1>
      </div>
    </div>
  </div>

  <div class="content">
    @if (Session::has('message'))
      <div class="alert alert-success border">
        {{ Session::get('message') }}
      </div>
    @endif

    <b-row>
      <b-col lg="4">
        <b-block full>
          <template slot="content">
            <form action="{{ route('profile.update', $user->id) }}" method="post">
              @csrf
              @method('put')
              <b-form-group label="Login" description="Taki sam jak w EKSoft GT">
                <b-form-input name="login" type="text" value="{{ $user->email }}" trim required></b-form-input>
              </b-form-group>
              <b-row>
                <b-col>
                  <b-form-group label="Nowe hasło">
                    <b-form-input id="new_password" name="new_password" type="password" trim></b-form-input>
                  </b-form-group>
                </b-col>
                <b-col>
                  <b-form-group label="Powtórz hasło">
                    <b-form-input id="new_password2" name="new_password2" type="password" trim></b-form-input>
                  </b-form-group>
                </b-col>
              </b-row>
              <b-form-group label="Konto technika">
                <select name="technik_id" class="form-control" required>
                  <option value="0">-- Brak --</option>
                  @foreach ($technicy as $technik)
                    <option value="{{ $technik->id }}" {{ ($technik->id == $user->technik_id) ? 'selected' : '' }}>
                      {{ $technik->nazwa }}
                    </option>
                  @endforeach
                </select>
              </b-form-group>

              <b-button type="submit" variant="primary">Zapisz</b-button>
            </form>
          </template>
        </b-block>
      </b-col>
    </b-row>
  </div>
@endsection

@section('js_after')<script>$(function(){

const password = document.getElementById('new_password')
const confirm_password = document.getElementById('new_password2')

function validatePassword() {
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity('Hasło nie zgadza się')
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;

})</script>@append
