@extends('admin.layout.auth')
@section('title', __('admin.mfa.verify_title'))
@section('page-content')
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <form class="md-float-material form-material" method="post" action="{{ route('post:admin:mfa.verify') }}">
                        @csrf
                        <div class="auth-box card">
                            <div class="card-block">
                                <h3 class="text-center txt-primary">{{ __('admin.mfa.heading_verify') }}</h3>
                                <p class="text-muted text-center">{{ __('admin.mfa.hint_verify') }}</p>
                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                <div class="form-group form-primary">
                                    <input type="text" name="code" class="form-control" required autofocus maxlength="16" placeholder="123456">
                                </div>
                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">{{ __('admin.mfa.btn_verify') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
