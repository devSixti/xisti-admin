@extends('admin.layout.auth')
@section('title')
    {{ __('admin.auth.login_title') }} — {{ config('xisti.product_name', 'XISTI') }}
@endsection
@section('page-content')
    <section class="login-block xisti-login-page">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                    <form class="md-float-material form-material" method="post" action="{{ route('post:admin:update_super_admin_login') }}">
                        {{ csrf_field() }}
                        @include('admin.include.brand_logo', ['showTagline' => true, 'class' => 'mb-3'])
                        <div class="auth-box card xisti-login-card">
                            <div class="card-block">
                                <h3 class="text-center">{{ __('admin.auth.login_title') }}</h3>
                                <p class="xisti-login-subtitle">{{ config('xisti.product_name', 'XISTI') }} Admin · {{ config('xisti.tagline') }}</p>
                                <div class="form-group form-primary fill-data">
                                    <input type="email" name="email" id="email" class="form-control fill" required autofocus autocomplete="username">
                                    <span class="form-bar"></span>
                                    <label class="float-label">{{ __('admin.auth.email') }}</label>
                                    <span class="error">{{ $errors->first('email') }}</span>
                                </div>
                                <div class="form-group form-primary fill-data">
                                    <input type="password" name="password" id="password" class="form-control fill" required autocomplete="current-password">
                                    <span class="form-bar"></span>
                                    <label class="float-label">{{ __('admin.auth.password') }}</label>
                                    <span class="error">{{ $errors->first('password') }}</span>
                                </div>
                                <input type="hidden" name="roles" value="1">
                                <div class="row m-t-10">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-md btn-block waves-effect text-center m-b-10">
                                            {{ strtoupper(__('admin.common.submit')) }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
