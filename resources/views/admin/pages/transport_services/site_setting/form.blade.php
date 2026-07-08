@extends('admin.layout.super_admin')
@section('title', __('admin.pages.service_setting'))
@section('page-css')
@endsection
@section('page-content')
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-green"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.pages.service_setting') }}</h5>
                            <span>@if(!isset($service_settings)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'service_setting') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'service_setting') }}@endif
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
                              action="{{ route('post:admin:update_service_setting') }}"
                              enctype="multipart/form-data">
                            {{csrf_field() }}
                            @if(isset($service_settings))
                                <input type="hidden" name="id" value="{{$service_settings->id}}"
                                       placeholder="{{ __('admin.forms.service_setting_id') }}">
                            @endif
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>@if(!isset($service_settings)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'service_setting') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'service_setting') }}@endif
                                        </div>
                                        <div class="card-block">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.driver_search_radius_km') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="provider_search_radius"
                                                           required
                                                           id="provider_search_radius"
                                                           placeholder="{{ __('admin.forms.driver_search_radius') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->provider_search_radius : old('provider_search_radius') }}">
                                                    <span class="error">{{ $errors->first('provider_search_radius') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.admin_commission_pct') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="admin_commission"
                                                           required
                                                           id="admin_commission"
                                                           placeholder="{{ __('admin.forms.admin_commission') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->admin_commission : old('admin_commission') }}">
                                                    <small class="text-muted">Comisión global por defecto. Para tarifas por vehículo use
                                                        <a href="{{ route('get:admin:vehicle_commission_rates') }}">Comisiones por vehículo</a>.</small>
                                                    <span class="error">{{ $errors->first('admin_commission') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.admin_hail_commission_pct') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="admin_hail_commission"
                                                           required
                                                           id="admin_hail_commission"
                                                           placeholder="{{ __('admin.forms.admin_hail_commission') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->admin_hail_commission : old('admin_hail_commission') }}">
                                                    <span class="error">{{ $errors->first('admin_hail_commission') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.ride_expiry_mins') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="ride_expiry"
                                                           required
                                                           id="ride_expiry"
                                                           placeholder="{{ __('admin.forms.ride_expiry') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->ride_expiry : old('ride_expiry') }}">
                                                    <span class="error">{{ $errors->first('ride_expiry') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.nearest_ride_popup_km') }} :</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="nearest_ride_popup" min="0" step="0.1"
                                                           required
                                                           id="nearest_ride_popup"
                                                           placeholder="{{ __('admin.forms.nearest_ride_popup') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->nearest_ride_popup : old('nearest_ride_popup') }}">
                                                    <span class="error">{{ $errors->first('nearest_ride_popup') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.driver_biding_timeout_sec') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control"
                                                           name="driver_timeout" min="0" step="0.1"
                                                           required
                                                           id="driver_timeout"
                                                           placeholder="{{ __('admin.forms.driver_biding_timeout') }}"
                                                           value="{{ (isset($service_settings)) ? $service_settings->driver_timeout : old('driver_timeout') }}">
                                                    <span class="error">{{ $errors->first('driver_timeout') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <center>
                                                <button type="submit" class="btn btn-success m-b-0">{{ __('admin.common.save') }}</button>
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
    <script type="text/javascript">
        $(document).ready(function () {
            $.validator.addMethod("greaterThanMin", function (value, element) {
                var minCashout = parseFloat($("#min_cashout").val());
                var maxCashout = parseFloat(value);
                return !isNaN(maxCashout) && !isNaN(minCashout) && maxCashout > minCashout;
            }, "Maximum Cashout must be greater than to Minimum Cashout.");

            $.validator.addMethod("threeDigitNumber", function (value, element) {
                    return this.optional(element) || /^[0-9]{1,3}$/.test(value);
                },
                "Please enter a valid 3-digit number."
            );

            $.validator.addMethod("uniqueValues", function (value, element) {
                const field1 = parseFloat($("#doc_expiry_warning_one").val());
                const field2 = parseFloat($("#doc_expiry_warning_two").val());
                const field3 = parseFloat($("#doc_expiry_warning_three").val());
                return field1 !== field2 && field1 !== field3 && field2 !== field3;
            }, "All fields must have different values.");

            $("#main").validate({
                rules: {
                    provider_search_radius: {
                        required: true,
                        min: 1,
                    },
                    admin_commission: {
                        required: true,
                        min: 0,
                    },
                    admin_hail_commission: {
                        required: true,
                        min: 0,
                    },
                    ride_expiry: {
                        required: true,
                        min: 1,
                    },
                    nearest_ride_popup: {
                        required: true,
                        min: 1,
                    },
                    driver_timeout: {
                        required: true,
                        min: 1,
                    },
                },
                submitHandler: function (form) {
                    $('.buttonloader').attr("disabled", true);
                    $('.buttonloader').html("<i class='fa fa-spinner fa-spin'></i>");
                    form.submit();
                }
            });
        });
    </script>
@endsection

