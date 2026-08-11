@extends('admin.layout.auth')
@section('title', __('admin.mfa.enroll_title'))
@section('page-content')
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <form class="md-float-material form-material" method="post" action="{{ route('post:admin:mfa.enroll') }}">
                        @csrf
                        <div class="auth-box card">
                            <div class="card-block">
                                <h3 class="text-center txt-primary">{{ __('admin.mfa.enroll_heading') }}</h3>
                                <p class="text-muted">{{ __('admin.mfa.enroll_hint') }}</p>
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="text-center m-b-20">
                                    <img src="{{ $qr_url }}" alt="TOTP QR" style="max-width:220px;">
                                </div>
                                <p><strong>{{ __('admin.mfa.manual_key') }}</strong> <code>{{ $secret }}</code></p>
                                <div class="form-group form-primary">
                                    <input type="text" name="code" class="form-control" required autofocus maxlength="6" placeholder="123456">
                                </div>
                                <button type="submit" class="btn btn-primary btn-md btn-block">{{ __('admin.mfa.btn_confirm_enrollment') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
