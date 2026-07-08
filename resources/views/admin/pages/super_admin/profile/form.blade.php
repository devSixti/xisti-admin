@extends('admin.layout.all_admin')
@section('title', __('admin.pages.profile'))
@section('page-css')
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>@if(isset($admin_details)) {{ $admin_details->name }} @endif Profile</h5>
                            <span>{{ __('admin.pages.profile') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <!-- Page body start -->
                    <div class="page-body">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('admin.pages.profile') }}</h5>
                                {{--<a href="{{ route('get:admin:user_list') }}"--}}
                                {{--class="btn btn-primary m-b-0 btn-right render_link"> Back</a>--}}
                            </div>
                            <div class="card-block">
                                <form id="main" method="post"
                                      action="@if(Illuminate\Support\Facades\Auth::guard("admin")->check()) @if(Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 2) {{ route('post:dispatcher:profile') }} @elseif(Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 3) {{ route('post:account:profile') }} @endif @endif"
                                      enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    <div class="row">
                                        <div class="form-group col-sm-7">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.columns.name') }}:<sup
                                                            class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="name" required
                                                           id="name" placeholder="{{ __('admin.columns.name') }}"
                                                           value="{{ (isset($admin_details)) ? $admin_details->name : old('name') }}">
                                                    <span class="error">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.mfa.email_label') }}<sup
                                                            class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="email" required
                                                           id="email" placeholder="{{ __('admin.forms.unique_email') }}" readonly
                                                           value="{{ (isset($admin_details)) ? $admin_details->email : old('email') }}">
                                                    <span class="error">{{ $errors->first('email') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary m-b-0">{{ __('admin.common.save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>

@endsection
@section('page-js')
@endsection

