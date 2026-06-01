@extends('admin.layout.auth')
@section('title')
    Driver Provider Register
@endsection
@section('page-css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <style>
        .login-block .auth-box {
            max-width: 650px;
        }

        .error {
            color: red;
        }
        .btn-success {
            background-color: #FFB600;
            border-color: #DB9E03;
        }

        .btn-success:hover, .btn-success:active, .btn-success:focus {
            background-color: #DB9E03;
            border-color: #DB9E03;
        }

    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/country-code/intlTelInput.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/country-code/demo.css')}}">
    <style>
        #phone:focus {
            border-bottom: 2px solid #00aff0
        }
    </style>
@endsection
@section('page-content')
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <form class="md-float-material form-material" action="{{ route('post:driver-admin:sign_up_pending') }}" method="post">
                        {{ csrf_field() }}
                        <div class="text-center webLogo" >
                            @if(isset($general_settings) && $general_settings->website_logo != Null)
                                <img src="{{ asset('assets/images/website-logo-icon/'.$general_settings->website_logo)}}" alt="{{$general_settings->website_logo}}">
                            @endif
                        </div>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center txt-primary">Sign up As Driver Provider</h3>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <input type="tel" name="contact_number" id="phone" class="form-control {{ $errors->first()!= Null ? "fill" : '' }}" required="" readonly disabled value="{{ Illuminate\Support\Facades\Auth::guard('driver')->user()->country_code.Illuminate\Support\Facades\Auth::guard('driver')->user()->contact_number }}">
                                            <input type="hidden" id="contact_numbers" name="contact_numbers" value="{{ Illuminate\Support\Facades\Auth::guard('driver')->user()->contact_number }}">
                                            <input type="hidden" id="country_code" name="country_code" value="{{ Illuminate\Support\Facades\Auth::guard('driver')->user()->country_code }}">
                                            <span class="form-bar"></span>

                                            {{--<label class="float-label">Contact Number</label>--}}
                                            <span id="phone_error" class="error">{{ $errors->first('contact_number') }}</span>
                                            <span class="error">{{ $errors->first('full_number') }}</span>
                                            <span class="error">{{ $errors->first('contact_numbers') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <input type="email" name="email" class="form-control {{ $errors->first()!= Null ? "fill" : '' }}" required="" value="{{old('email') }}">
                                            <span class="form-bar"></span>
                                            <label class="float-label">Email</label>
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <input type="text" name="name"
                                                   class="form-control {{ $errors->first()!= Null ? "fill" : '' }}"
                                                   required=""
                                                   value="{{old('name') }}">
                                            <span class="form-bar"></span>
                                            <label class="float-label">Name</label>
                                            <span class="error">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-primary">
                                            <div class="row"
                                                 style="margin:0;border-bottom: 1px solid #ccc">
                                                <label class="col-sm-4 col-form-label"
                                                       style="padding-left: 0 ; padding-top: 10px">Gender:<sup
                                                            class="error">*</sup></label>
                                                <div class="col-sm-8">
                                                    <div class="form-radio">

                                                        <div class="row">
                                                            <div class="radio radio-inline"
                                                                 style="padding-left: 0 ; padding-top: 7px">
                                                                <label>
                                                                    <input type="radio" value="1"
                                                                           {{old('gender') == '1' ? "checked ": "" }}
                                                                           name="gender" {{ (isset($user_details)) ? ($user_details->gender == 1)? "checked ": "" : ""}}>
                                                                    <i class="helper"></i>Male
                                                                </label>
                                                            </div>
                                                            <div class="radio radio-inline"
                                                                 style="padding-left: 0 ; padding-top: 7px">
                                                                <label>
                                                                    <input type="radio" value="2"
                                                                           {{old('gender') == '2' ? "checked ": "" }}
                                                                           name="gender" {{ (isset($user_details)) ? ($user_details->gender == 2)? "checked ": "" : ""}}>
                                                                    <i class="helper"></i>Female
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="error">{{ $errors->first('gender') }}</span>
                                                <span class="form-bar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button class="btn btn-success btn-md btn-block waves-effect text-center m-b-20">Sign up now</button>
                                    </div>
                                </div>
                                <p class="text-inverse text-left"><a href="{{ route('driver:logout',[ 'driver' ]) }}"><b style="color: mediumseagreen">Sign Out </b></a></p>
                                {{--<p class="text-inverse text-left">Already have an account?<a href="{{ route('get:driver-admin:login') }}"><b style="color: mediumseagreen">Sign In </b></a>here!</p>--}}
                            </div>
                        </div>
                    </form>
                    <!-- Authentication card end -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
@endsection
@section('page-js')
    <script type="text/javascript" src="{{ asset('assets/js/country-code/intlTelInput.min.js')}}"></script>
    <script type="text/javascript">
        var input = document.querySelector("#phone");
        var iti  = window.intlTelInput(input, {
            // allowDropdown: false,
            // autoHideDialCode: false,
            // autoPlaceholder: "off",
            // dropdownContainer: document.body,
            // excludeCountries: ["us"],
            // formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //     var countryCode = (resp && resp.country) ? resp.country : "";
            //     callback(countryCode);
            //   });
            // },
            hiddenInput: "full_number",
            initialCountry: "CO",
            // localizedCountries: { 'de': 'Deutschland' },
            // nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            // preferredCountries: ['ph'],
            separateDialCode: true,
            // initialCountry: "auto",
            // geoIpLookup: function(success, failure) {
            //     $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //         var countryCode = (resp && resp.country) ? resp.country : "";
            //         success(countryCode);
            //     });
            // },
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
                // var n = contact_number.indexOf("0", 0);
                // // var n = contact_number.charAt(contact_number);
                // if (n == 0) {
                //     document.getElementById("phone_error").innerHTML = 'Invalid Contact Number';
                //     document.getElementById("contact_numbers").value = "";
                // } else {
                //     document.getElementById("contact_numbers").value = contact_number;
                //     document.getElementById("phone_error").innerHTML = '';
                //     console.log(contact_number);
                // }

                //check code for numeric value
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
