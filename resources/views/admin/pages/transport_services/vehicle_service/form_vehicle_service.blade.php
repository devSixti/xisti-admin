@extends('admin.layout.super_admin')
@section('title')
    @if(!isset($transport_vehicle_service)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'vehicle_service') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'vehicle_service') }}@endif
@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/css/plugin/mdtimepicker.min.css')}}" type="text/css">
    <style>
        .answer {
            display:none
        }
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
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-green"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.pages.vehicle_service') }}</h5>
                            <span>@if(!isset($transport_vehicle_service)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'vehicle_service') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'vehicle_service') }}@endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <!-- Page body start -->
                    <div class="page-body">
                        <form id="main" class="add_vehicle_service" method="post" action="{{ route('post:admin:update_transport_vehicle_service') }}" enctype="multipart/form-data">
                            {{csrf_field() }}
                            @if(isset($transport_vehicle_service))
                                <input type="hidden" name="id" value="{{$transport_vehicle_service->id}}">
                            @endif
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>@if(!isset($transport_vehicle_service)){{ \App\Helpers\AdminUi::formEntityTitle(true, 'vehicle_service') }}@else{{ \App\Helpers\AdminUi::formEntityTitle(false, 'vehicle_service') }}@endif</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.vehicle_service_name') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="name" required
                                                           id="name" placeholder="{{ __('admin.forms.unique_vehicle_service_name') }}"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->name : old('name') }}">
                                                    <span class="error">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.service_category_lower') }}:</label>
                                                <div class="col-sm-8">
                                                    <select class="form-control" name="service_mode" id="service_mode">
                                                        <option value="transport" {{ (isset($transport_vehicle_service) && ($transport_vehicle_service->service_mode ?? 'transport') == 'transport') ? 'selected' : '' }}>{{ __('admin.vehicle_service.mode_transport') }}</option>
                                                        <option value="delivery" {{ (isset($transport_vehicle_service) && ($transport_vehicle_service->service_mode ?? '') == 'delivery') ? 'selected' : '' }}>{{ __('admin.vehicle_service.mode_delivery') }}</option>
                                                        <option value="viajes_compartidos" {{ (isset($transport_vehicle_service) && in_array($transport_vehicle_service->service_mode ?? '', ['expreso','viajes_compartidos'], true)) ? 'selected' : '' }}>{{ __('admin.vehicle_service.mode_shared') }}</option>
                                                        <option value="encomiendas" {{ (isset($transport_vehicle_service) && ($transport_vehicle_service->service_mode ?? '') == 'encomiendas') ? 'selected' : '' }}>{{ __('admin.vehicle_service.mode_encomiendas') }}</option>
                                                        <option value="acarreos" {{ (isset($transport_vehicle_service) && ($transport_vehicle_service->service_mode ?? '') == 'acarreos') ? 'selected' : '' }}>{{ __('admin.vehicle_service.mode_acarreos') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.app_display_order') }}:</label>
                                                <div class="col-sm-8">
                                                    <input type="number" min="0" class="form-control" name="display_order"
                                                           value="{{ (isset($transport_vehicle_service)) ? ($transport_vehicle_service->display_order ?? 0) : old('display_order', 0) }}">
                                                    <small class="form-text text-muted">
                                                        {{ __('admin.forms.display_order_hint') }}
                                                    </small>
                                                </div>
                                            </div>

                                            @if(isset($language_lists))
                                                @foreach($language_lists as $single_lang)
                                                    @php
                                                        $language_name =  isset($single_lang->language_name)?$single_lang->language_name:"";
                                                        $language_code =  isset($single_lang->language_code)?$single_lang->language_code:"";
                                                        $col_name = $language_code."_name";
                                                    @endphp
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">{{ __('admin.forms.vehicle_service_name_in', ['lang' => $language_name]) }}:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="{{$col_name}}" required id="{{$col_name}}" placeholder="{{ __('admin.forms.unique_category_name_in', ['lang' => $language_name]) }}" value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->$col_name : old('ar_name') }}">
                                                            <span class="error">{{ $errors->first($col_name) }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.cost_for_km') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="cost_for_km"
                                                           required
                                                           id="cost_for_km" placeholder="{{ __('admin.forms.cost_for_km') }}" min="0"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->cost_for_km : old('cost_for_km') }}">
                                                    <span class="error">{{ $errors->first('cost_for_km') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.time_fare_per_min') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="time_fare"
                                                           required
                                                           id="time_fare" placeholder="{{ __('admin.forms.time_fare_per_min_ph') }}"
                                                           step="0.01"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->time_fare : old('time_fare') }}">
                                                    <span class="error">{{ $errors->first('time_fare') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.minimum_offer_fare_pct') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="max_bargain_percent"
                                                           required
                                                           id="max_bargain_percent" placeholder="{{ __('admin.forms.max_bargain_percent') }}" min="0"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->max_bargain_percent : old('max_bargain_percent') }}">
                                                    <span class="error">{{ $errors->first('max_bargain_percent') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.max_offer_fare_pct') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="max_offer_percent"
                                                           required
                                                           id="max_offer_percent" placeholder="{{ __('admin.forms.max_bargain_percent') }}" min="0"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->max_offer_percent : old('max_offer_percent') }}">
                                                    <span class="error">{{ $errors->first('max_offer_percent') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.columns.minimum_fare') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="min_fare"
                                                           required
                                                           id="min_fare" placeholder="{{ __('admin.columns.minimum_fare') }}" min="0"
                                                           value="{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->min_fare : old('min_fare') }}">
                                                    <span class="error">{{ $errors->first('min_fare') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.vehicle_service_image') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    @if(isset($transport_vehicle_service) && $transport_vehicle_service->icon_name != Null)
                                                        <div class="col-sm-4">
                                                            <img src="{{ asset('/assets/images/vehicle-service/'.$transport_vehicle_service->icon_name)}}"
                                                                 style="width: 50px; height: 50px">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" name="icon"
                                                           id="icon"
                                                           @if(!isset($transport_vehicle_service)) required @endif>
                                                    <span class="note">{{ __('admin.forms.icon_upload_note') }}</span>
                                                    <span class="error">{{ $errors->first('icon') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.vehicle_service_icon') }}:<sup class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    @if(isset($transport_vehicle_service) && $transport_vehicle_service->vehicle_service_icon != Null)
                                                        <div class="col-sm-4">
                                                            <img src="{{ asset('/assets/images/vehicle-service/'.$transport_vehicle_service->vehicle_service_icon)}}"
                                                                 style="width: 50px; height: 50px">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" name="vehicle_service_icon"
                                                           id="vehicle_service_icon"
                                                           @if(!isset($transport_vehicle_service)) required @endif>
                                                    <span class="note">{{ __('admin.forms.icon_upload_note') }}</span>
                                                    <span class="error">{{ $errors->first('vehicle_service_icon') }}</span>
                                                </div>
                                            </div>

{{--                                            <div class="form-group row">--}}
{{--                                                <label class="col-sm-4 col-form-label">Courier Services</label>--}}
{{--                                                <div class="col-sm-8">--}}
{{--                                                    <div class="border-checkbox-group border-checkbox-group-primary">--}}
{{--                                                        <img src="{{ asset('/assets/images/service-category/courier-service.png') }}" class="provider_check_icon" width="40px" height="40px">--}}
{{--                                                        <input name="courier_services" class="border-checkbox goog-te-gadget-icon ml-2 " type="checkbox" id="courier_services"--}}
{{--                                                            {{ old('courier_services', isset($transport_vehicle_service) && $transport_vehicle_service->courier_services == 'on') ? 'checked' : '' }}>--}}
{{--                                                        <label class="border-checkbox-label" for="courier_boy"></label>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.forms.service_description') }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <textarea rows=7 class="form-control" name="vehicle_service_description" id="vehicle_service_description" placeholder="{{ __('admin.forms.service_description') }}" required>{{ (isset($transport_vehicle_service)) ? $transport_vehicle_service->vehicle_service_description : old('vehicle_service_description') }}</textarea>
                                                    <span class="error">{{ $errors->first('vehicle_service_description') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">{{ __('admin.common.status') }}:</label>
                                                <div class="col-sm-8">
                                                    <select name="status" id="status" class="form-control"
                                                            required>
                                                        @if(isset($transport_vehicle_service) && $transport_vehicle_service->status==0)
                                                            <option value="1">{{ __('admin.forms.activate') }}</option>
                                                            <option value="0" selected>{{ __('admin.forms.deactivate') }}</option>
                                                        @else
                                                            <option value="1" selected>{{ __('admin.forms.activate') }}</option>
                                                            <option value="0">{{ __('admin.forms.deactivate') }}</option>
                                                        @endif
                                                    </select>
                                                    <span class="error">{{ $errors->first('status') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        {{--<label class="col-sm-4 col-form-label"></label>--}}
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
    <script>
        // form validation
        $(document).ready(function () {
            $(".add_vehicle_service").validate({
                rules: {
                    // Existing rules
                    name: {
                        required: true,
                    },
                    cost_for_km: {
                        required: true,
                        number: true,
                    },
                    time_fare: {
                        required: true,
                        number: true,
                        min:0
                    }
                },
                messages: {
                    // Existing messages
                    name: {
                        required: "Please enter a name.",
                    },
                    cost_for_km: {
                        required: "Please enter the cost per km.",
                        number: "Please enter a valid number.",
                    },
                    // time_fare: {
                    //     required: "Please enter the time fare.",
                    //     number: "Please enter a valid number.",
                    //     min: "Please enter a value greater than or equal to 0."
                    // }
                },
                submitHandler: function (form) {
                    $('.buttonloader').attr("disabled", true);
                    $('.buttonloader').html("<i class='fa fa-spinner fa-spin'></i>");
                    form.submit();
                },
                errorPlacement: function (error, element) {
                    let errorId = $(element).data("error");
                    if (errorId) {
                        $(errorId).html(error); // Use data-error attribute for placement
                    } else {
                        error.insertAfter(element); // Default placement
                    }
                }
            });
        });
    </script>
@endsection

