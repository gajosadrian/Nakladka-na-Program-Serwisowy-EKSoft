@extends('global.app', [ 'window' => true ])

@section('content')
    <div class="row no-gutters justify-content-center bg-body-dark">
        <div class="hero-static col-sm-10 col-md-8 col-xl-6 d-flex align-items-center p-2 px-sm-0">
            <!-- Sign In Block -->
            <div class="block block-rounded block-transparent block-fx-pop w-100 mb-0 overflow-hidden bg-image" style="background-image: url('{{ asset('media/photos/photo20.jpg') }}')">
                <div class="row no-gutters">
                    <div class="col-md-6 order-md-1 bg-white">
                        <div class="block-content block-content-full px-lg-5 py-md-5 py-lg-6">
                            <!-- Header -->
                            <div class="mb-2 text-center">
                                <p class="text-uppercase font-w700 font-size-sm text-muted">Logowanie</p>
                            </div>
                            <!-- END Header -->

                            <!-- Sign In Form -->
                            <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js) -->
                            <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                            <form class="js-validation-signin" action="{{ route('login') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-alt" name="email" placeholder="Login">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-alt" name="password" placeholder="Hasło">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-block btn-hero-primary">
                                        <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Zaloguj
                                    </button>
                                </div>
                            </form>
                            <!-- END Sign In Form -->
                        </div>
                    </div>
                    <div class="col-md-6 order-md-0 bg-primary-dark-op d-flex align-items-center">
                        <div class="block-content block-content-full px-lg-5 py-md-5 py-lg-6">
                            <div class="media">
                                <a class="img-link mr-3" href="javascript:void(0)">
                                    <img class="img-avatar img-avatar-thumb" src="{{ asset('media/avatars/avatar0.jpg') }}" alt="">
                                </a>
                                <div class="media-body">
                                    <p class="text-white font-w600 mb-1">
                                        {{ program()->description }}
                                    </p>
                                    <a class="text-white-75 font-w600" href="javascript:void(0)">{{ program()->name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Sign In Block -->
        </div>
    </div>
@endsection
