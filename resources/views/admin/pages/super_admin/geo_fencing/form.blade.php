@extends('admin.layout.super_admin')
@section('title')
    @if(isset($area_details)) Edit @else Add @endif Restricted Area
@endsection
@section('page-css')
    <style>
        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + .label {
            position: relative;
            /*margin-left: 43%;*/
            /*display: block;*/
            padding-left: 25px;
            margin-right: 10px;
            cursor: pointer;
            /*line-height: 16px;*/
            color: black;
            font-size: 14px;
            transition: all .2s ease-in-out;
            margin-bottom: 10px;
        }

        input[type="radio"] + .label:before, input[type="radio"] > .label:after {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 20px;
            height: 20px;
            text-align: center;
            color: black;
            cursor: pointer;
            border-radius: 50%;
            transition: all .3s ease;
        }

        input[type="radio"] + .label:before {
            /*box-shadow: inset 0 0 0 1px #666565, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;*/
            box-shadow: 0 0 0 0 #91DEAC, inset 0 0 0 2px #FFFFFF, inset 0 0 0 3px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
        }

        input[type="radio"] + .label:hover {
            color: #44BB6E;
        }

        input[type="radio"] + .label:hover:before {
            animation-duration: .5s;
            animation-name: change-size;
            animation-iteration-count: infinite;
            animation-direction: alternate;
            box-shadow: inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
        }

        input[type="radio"]:checked + .label:hover {
            color: #333333;
            cursor: default;
        }

        input[type="radio"]:checked + .label:before {
            animation-duration: .2s;
            animation-name: select-radio;
            animation-iteration-count: 1;
            animation-direction: Normal;
            box-shadow: inset 0 0 0 1px #44BB6E, inset 0 0 0 3px #FFFFFF, inset 0 0 0 16px #44BB6E;

        }

        @keyframes change-size {
            from {
                box-shadow: 0 0 0 0 #44BB6E, inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            to {
                box-shadow: 0 0 0 1px #44BB6E, inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
        }

        @keyframes select-radio {
            0% {
                box-shadow: 0 0 0 0 #91DEAC, inset 0 0 0 2px #FFFFFF, inset 0 0 0 3px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            90% {
                box-shadow: 0 0 0 10px #E8FFF0, inset 0 0 0 0 #FFFFFF, inset 0 0 0 1px #44BB6E, inset 0 0 0 2px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            100% {
                box-shadow: 0 0 0 12px #E8FFF0, inset 0 0 0 0 #FFFFFF, inset 0 0 0 1px #44BB6E, inset 0 0 0 3px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
        }

        @media screen and (max-width: 576px) {
            input[type="radio"] + .label {
                margin-left: 48%;
                display: block;
            }
        }
        #searchInput,#suggestions {
            background-color: #fff;
            font-family: Roboto;
            font-size: 16px;
            font-weight: 300;
            /*margin-left: 12px;*/
            /*padding: 0 11px 0 13px;*/
            text-overflow: ellipsis;
            width: 100%;
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
                            <h5>Restricted Area</h5>
                            <span>@if(isset($area_details)) Edit @else Add @endif Restricted Area</span>
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
                                <h5>@if(isset($area_details)) Edit @else Add @endif Restricted Area</h5>
                                <a href="{{ route('get:admin:restricted_area_list') }}"
                                   class="btn btn-primary m-b-0 btn-right render_link"> Back</a>
                            </div>
                            <div class="card-block">
                                <form id="main" method="post" action="{{ route('post:admin:update_restricted_area') }}"
                                      enctype="multipart/form-data">
                                    {{csrf_field() }}

                                    @if(isset($area_details))
                                        <input type="hidden" name="id" value="{{$area_details->id}}">
                                    @endif
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Restricted Area Name:<sup class="error">*</sup></label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="area_name" required id="area_name" placeholder="Restricted Area Name"
                                                           value="{{ (isset($area_details)) ? $area_details->name : old('name') }}">
                                                    <span class="error">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                                            {{--<div class="form-group row">--}}
                                            {{--    <label class="col-sm-3 col-form-label">Restrict Location:<sup class="error">*</sup></label>--}}
                                            {{--    <div class="col-sm-9">--}}
                                            {{--        <select name="restrict_location" id="restrict_location" class="form-control" required>--}}
                                            {{--            <option value="1" {{ (isset($area_details)) && $area_details->restrict_location == 1 ? "selected" : '' }} >Pick Up</option>--}}
                                            {{--            <option value="2" {{ (isset($area_details)) && $area_details->restrict_location == 2 ? "selected" : '' }} >Drop Off</option>--}}
                                            {{--            <option value="0" {{ (isset($area_details)) && $area_details->restrict_location == 0 ? "selected" : '' }} >All</option>--}}
                                            {{--        </select>--}}
                                            {{--        <span class="error">{{ $errors->first('restrict_location') }}</span>--}}
                                            {{--    </div>--}}
                                            {{--</div>--}}
                                            {{--<div class="form-group row">--}}
                                            {{--    <label class="col-sm-3 col-form-label">Restrict Type:<sup class="error">*</sup></label>--}}
                                            {{--    <div class="col-sm-9">--}}
                                            {{--        <select name="restrict_type" id="restrict_type" class="form-control" required>--}}
                                            {{--            <option value="1" {{ (isset($area_details)) && $area_details->restrict_type == 1 ? "selected" : '' }} >Activate</option>--}}
                                            {{--            <option value="0" {{ (isset($area_details)) && $area_details->restrict_type == 0 ? "selected" : '' }} >Deactivate</option>--}}
                                            {{--        </select>--}}
                                            {{--        <span class="error">{{ $errors->first('restrict_type') }}</span>--}}
                                            {{--    </div>--}}
                                            {{--</div>--}}
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Status:<sup class="error">*</sup></label>
                                                <div class="col-sm-9">
                                                    <select name="status" id="status" class="form-control" required>
                                                        <option value="1" {{ (isset($area_details)) && $area_details->status == 1 ? "selected" : '' }} >On</option>
                                                        <option value="0" {{ (isset($area_details)) && $area_details->status == 0 ? "selected" : '' }} >Off</option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('status') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="latitude" id="lat" value="{{ isset($area_details) ? $area_details->latitude : '' }}">
                                        <input type="hidden" name="longitude" id="lang" value="{{ isset($area_details) ? $area_details->longitude : '' }}">
                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input id="searchInput" name="store_address" class="input-controls form-control my-2"
                                                               value="{{ (isset($store_details)) ? $store_details->store_address : old('store_address')}}" type="text" placeholder="Enter a location">
                                                        <!-- Suggestions dropdown -->
                                                        <div id="suggestions"></div>
                                                        <div class="map" id="map" style="width: 100%; height: 500px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <center>
                                                <button type="submit" class="btn btn-primary m-b-0 buttonloader">Save</button>
                                                <button type="button" class="btn btn-danger" id="reset">Reset</button>
                                            </center>
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
    <script>
        var lati = parseFloat('{{ $general_settings->address_lat ?? "0" }}');
        var longi = parseFloat('{{ $general_settings->address_long ?? "0" }}');
        // Set address dynamically in the input field using JavaScript
        {{--        var address = @json(isset($provider_other_details) ? $provider_other_details->address : old('address'));--}}
        var apiKey = '{{ $general_settings->map_key }}';
    </script>
    {{-- JS for the map --}}
    <script type="text/javascript" src="{{ asset('assets/js/google-map/geofencing-google-map-autocomplete.js?v=0.4')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ isset($general_settings)? ($general_settings->map_key != Null)? $general_settings->map_key : 0 : 0 }}&callback=initMap&v=weekly" defer ></script>

@endsection
