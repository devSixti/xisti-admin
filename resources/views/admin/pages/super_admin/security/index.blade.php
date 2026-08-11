@extends('admin.layout.super_admin')
@section('title', __('admin.mfa.security_title'))
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-shield bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.mfa.security_title') }}</h5>
                            <span>{{ __('admin.mfa.security_subtitle') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">
                        <div class="card">
                            <div class="card-block">
                                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                                @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
                                @if(empty($mfa_schema_ready))
                                    <div class="alert alert-warning">{{ __('admin.mfa.schema_missing') }}</div>
                                @endif
                                <p><strong>{{ __('admin.mfa.email_label') }}</strong> {{ $admin->email }}</p>
                                <p><strong>{{ __('admin.mfa.status_label') }}</strong> {{ $mfa_enrolled ? __('admin.mfa.status_enrolled') : __('admin.mfa.status_not_enrolled') }}</p>
                                @if(!empty($mfa_schema_ready))
                                    @if($mfa_enrolled)
                                        <form method="post" action="{{ route('post:admin:mfa.reset') }}" onsubmit="return confirm(@json(__('admin.mfa.confirm_reset')));">
                                            @csrf
                                            <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                                            <button type="submit" class="btn btn-warning">{{ __('admin.mfa.btn_reset') }}</button>
                                        </form>
                                    @else
                                        <a href="{{ route('get:admin:mfa.enroll') }}" class="btn btn-primary">{{ __('admin.mfa.btn_enroll') }}</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
