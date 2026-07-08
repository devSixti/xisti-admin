@extends('admin.layout.super_admin')
@section('title', __('admin.pages.city_area_setting'))
@section('page-css')
    <style>
        /*checkbox style*/
        .border-checkbox-section .border-checkbox-group .border-checkbox-label {
            height: 7px;
            padding-left: 30px;
            margin-right: 7px;
        }
        .border-checkbox-section .border-checkbox-group {
            margin-right: 15px;
        }
        .border-checkbox-section .border-checkbox-group .checklbl {
            height: 0;
        }
    </style>
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
                            <h5>{{ __('admin.pages.city_area_setting') }}</h5>
                            <span>@if(!isset($city_area_settings)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'city_area_setting') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'city_area_setting') }}@endif
                            </span>
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
                        <form id="main" method="post"
                              action="{{ route('post:admin:update_city_area_setting') }}">
                            {{csrf_field() }}
                            @if(isset($id))
                                <input type="hidden" name="id" value="{{$id}}"
                                       placeholder="{{ __('admin.forms.city_area_setting_id') }}">
                            @endif
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>@if(!isset($city_area_settings)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'city_area_setting') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'city_area_setting') }}@endif</h5>
                                            <a href="{{ route('get:admin:city_area_list') }}" class="btn btn-primary m-b-0 btn-right render_link">{{ __('admin.common.back') }}</a>
                                        </div>
                                        <div class="card-block">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.admin_commision_pct') }}
                                                    (in
                                                    %):</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="admin_commission"
                                                           id="admin_commission"
                                                           placeholder="{{ __('admin.forms.admin_commission') }}"
                                                           value="{{ (isset($city_area_settings)) ? $city_area_settings->admin_commission : old('admin_commission') }}">
                                                    <span class="error">{{ $errors->first('admin_commission') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.payment_method') }}:</label>
                                                @if(request()->get("general_settings")->cash_payment != null)
                                                <div class="col-sm-2 border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input type="hidden" name="cash" value="0">
                                                        <input type="checkbox" value="1" class="border-checkbox" {{ isset($city_area_settings) && $city_area_settings->cash_payment == 1 ? 'checked' : '' }} name="cash" id="cash">
                                                        <label class="border-checkbox-label" for="cash"> </label>
                                                        <span>{{ __('admin.forms.cash') }}</span>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(request()->get("general_settings")->card_payment != null)
                                                <div class="col-sm-2 border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input type="hidden" name="card" value="0">
                                                        <input type="checkbox" value="1" class="border-checkbox" {{ isset($city_area_settings) && $city_area_settings->card_payment == 1 ? 'checked' : '' }} name="card" id="card">
                                                        <label class="border-checkbox-label" for="card"> </label>
                                                        <span>{{ __('admin.forms.card') }}</span>
                                                    </div>
                                                </div>
                                                @endif
                                                @if(request()->get("general_settings")->wallet_payment != null)
                                                <div class="col-sm-2 border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input type="hidden" name="wallet" value="0">
                                                        <input type="checkbox" value="1" class="border-checkbox" {{ isset($city_area_settings) && $city_area_settings->wallet_payment == 1 ? 'checked' : '' }} name="wallet" id="wallet">
                                                        <label class="border-checkbox-label" for="wallet"> </label>
                                                        <span>{{ __('admin.columns.wallet') }}</span>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-center"><span class="error error_checkbox hidden"></span></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <center>
                                                <button type="submit" class="btn btn-primary m-b-0">{{ __('admin.common.save') }}</button>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script>
        $(document).ready(function () {
            $("#main").validate({
                rules: {
                    admin_commission: {
                        required: true,
                        digits: true
                    }
                },
                messages: {
                    admin_commission: {
                        required: 'Please enter the admin commission amount.',
                        digits: "Enter Valid Admin Commission"
                    }
                },
                submitHandler: function (form) {
                    if (!$("input[name='cash']").is(":checked") && !$("input[name='card']").is(":checked") && !$("input[name='wallet']").is(":checked")) {
                        $('.error_checkbox').show();
                        $('.error_checkbox').text('Please Select at least one payment type');
                    } else {
                        $('.error_checkbox').hide();
                        form.submit();
                    }                }
            });
        });
    </script>
@endsection

