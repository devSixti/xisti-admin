@extends('admin.layout.super_admin')
@section('title')
    @if(!isset($sos))
        Add
    @else
        Edit
    @endif
    SOS
@endsection
@section('page-css')
    <style>
        .toggle input[type="checkbox"] + .button-indecator:before {
            font-size: 25px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/country-code/intlTelInput.css')}}">
    <style>
        #phone:focus {
            border-bottom: 2px solid #4099ff
        }
    </style>
@endsection
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>SOS</h5>
                            <span>@if(!isset($sos))Add @else Edit @endif SOS</span>
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
                            <div class="card-header">
                                <h5>@if(!isset($sos))Add @else Edit @endif SOS</h5>
                                <a href="{{ route('get:admin:sos') }}" class="btn btn-primary m-b-0 btn-right render_link"> Back</a>
                            </div>
                            <div class="card-block">
                                <form id="main" class="sos" method="post" action="{{route('post:admin:save_update_sos')}}" enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    @if(isset($sos))
                                        <input type="hidden" name="id" value="{{$sos->id}}">
                                    @endif
                                    <div class="row">
                                        <div class="form-group col-sm-7">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Name:<sup class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control"  name="name" required id="name" placeholder="Name" value="{{ (isset($sos)) ? $sos->name : old('name') }}">
                                                    <span class="error">{{ $errors->first('name') }}</span>
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
                                                        <label class="col-sm-4 col-form-label">Language Value (in {{$language_name}}):<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="{{$col_name}}" id="{{$col_name}}" value="{{ isset($sos->$col_name)?$sos->$col_name:"" }}"  placeholder="Please enter language (in {{$language_name}})" value="" autocomplete="off" required>
                                                            <span class="error">{{ $errors->first($col_name) }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Contact Number:<sup class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control phone"
                                                           onkeypress="return ((event.charCode >= 48 && event.charCode <= 57) || event.charCode === 13)"
                                                           name="contact_number" required id="phone" placeholder="Unique Contact Number" value="{{ (isset($sos)) ? $sos->country_code.App\Models\User::ContactNumber2Stars($sos->contact_number) : '' }}">
                                                    <input type="hidden" id="contact_numbers" name="contact_numbers" value="{{ (isset($sos)) ? $sos->country_code.$sos->contact_number : '' }}">
                                                    <input type="hidden" id="country_code" name="country_code" value="{{ (isset($sos)) ? $sos->country_code : '+57' }}">
                                                    <span id="phone_error" class="error">{{ $errors->first('contact_number') }}</span>
                                                    <label id="phone-error" class="error" for="phone"></label>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">SOS Status:</label>
                                                <div class="col-sm-8">
                                                    <select name="sos_status" id="sos_status"
                                                            class="form-control" required>
                                                            @if(isset($sos) && $sos->status == 0)
                                                                <option value="1">Activate</option>
                                                                <option value="0" selected>Deactivate</option>
                                                            @else
                                                                <option value="1" selected>Activate</option>
                                                                <option value="0">Deactivate</option>
                                                            @endif
                                                    </select>
                                                    <span class="error">{{ $errors->first('sos_status') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <center><button type="submit" class="btn btnsaveclick btn-primary m-b-0 buttonloader">Save</button></center>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script type="text/javascript">
        $(document).ready(function () {
            jQuery.validator.addMethod("name_regex", function(value, element, param) {
                return this.optional(element) || param.test(value);
            }, "Only letters, numbers, spaces, underscores, and dashes are allowed.");

            $(".sos").validate({
                rules: {
                    name: {
                        required: true,
                        name_regex: /^[a-zA-Z0-9 _-]+$/,
                        minlength: 5,
                        maxlength: 50
                    },
                    contact_number: {
                        required: true
                    }
                },
                submitHandler: function (form) {
                    $('.buttonloader').attr("disabled", true);
                    $('.buttonloader').html("<i class='fa fa-spinner fa-spin'></i>");
                    form.submit();
                }
            });

            $(".btnsaveclick").on("click", function () {
                if ($(".sos").valid()) {
                    $(this).attr("disabled", true);
                    $("#main").submit();
                }
            });
        });

    </script>
    <script type="text/javascript" src="{{ asset('assets/js/country-code/intlTelInput.min.js')}}"></script>
    <script>
        var default_country_code = "{{ ((isset($general_settings)) && $general_settings->default_country_code != Null ) ? $general_settings->default_country_code : 'CO'  }}";
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            formatOnDisplay: false,
            hiddenInput: "full_number",
            initialCountry: default_country_code,
            separateDialCode: true,
            utilsScript: "{{ asset('assets/js/country-code/utils.js')}}",
        });
        $(document).ready(function () {
            input.addEventListener("countrychange", function () {
                var country_code = iti.getSelectedCountryData()['dialCode'];
                if (country_code > 0) {
                    country_code = "+" + country_code;
                    document.getElementById("phone_error").innerHTML = '';
                } else {
                    country_code = "+57";
                    document.getElementById("phone_error").innerHTML = 'Invalid Country Code';
                }
                $("#country_code").val(country_code);
            });

            $("#phone").on('keyup', function (event) {
                var contact_number = $(this).val();
                if (isNaN(contact_number)) {
                    document.getElementById("phone_error").innerHTML = 'Invalid Contact Number';
                    document.getElementById("contact_numbers").value = "";
                } else {
                    document.getElementById("contact_numbers").value = contact_number;
                    document.getElementById("phone_error").innerHTML = '';
                }
            });
        });
    </script>
@endsection

