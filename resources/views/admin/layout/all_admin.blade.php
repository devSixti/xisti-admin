<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>@yield('title')</title>
    <!--== META TAGS ==-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>

    <!-- Favicon icon -->
    <link rel="icon" href="{{ isset($general_settings)? ($general_settings->website_favicon != Null) ? asset('assets/images/website-logo-icon/'.$general_settings->website_favicon) : '' : '' }}" type="image/x-icon">
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css')}}">
    <!-- waves.css -->
    <link rel="stylesheet" href="{{ asset('assets/css/waves.min.css')}}" type="text/css" media="all">
    <!-- feather icon -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/feather.css?v=0.1')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/font/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    @yield('extra-css-link')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css?v=0.1')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/pages.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/extra.style.css?v=0.3')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/extra.style2.css')}}">
    @yield('page-css')
    <style>
        .currency:before {
            content: 'COP';
        }
        @media only screen and (max-width: 576px) {
            #google_translate_element {
                position: absolute !important;
                /*right: 2px !important;*/
                top: 75px !important;
                left: 0 !important;
                z-index: 1000000 !important;
            }
            #render-content{
                padding-top: 25px;
            }
        }
    </style>
{{--    <script type='text/javascript' data-cfasync='false'>window.purechatApi = { l: [], t: [], on: function () { this.l.push(arguments); } }; (function () { var done = false; var script = document.createElement('script'); script.async = true; script.type = 'text/javascript'; script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript'; document.getElementsByTagName('HEAD').item(0).appendChild(script); script.onreadystatechange = script.onload = function (e) { if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) { var w = new PCWidget({c: '54190bda-b8e9-437e-9990-1ca4e22849be', f: true }); done = true; } }; })();</script>--}}
</head>
<body>
<div id="google_translate_element" style="position: absolute; right: 350px; top:12px; z-index: 1000000"></div>
<div id="render-css-link"></div>
<div id="render-css"></div>
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-bar"></div>
</div>
{{--without refresh page--}}
<div class="pre-loader"></div>
<!-- [ Pre-loader ] end -->
<div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">

        {{--navbar start--}}
        @include('admin.include.navbar')
        {{--navbar end--}}

        <div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
                        @include('admin.include.sidebar')
                @endif
                 @include('admin.include.store-sidebar')
                              {{--sidebar end--}}

                {{--content start--}}
                @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
                    <div id="render-content">
                        @yield('page-content')
                    </div>
                @endif
                {{--content end--}}
            </div>
        </div>
    </div>
</div>
<div id="render-js"></div>
<!--======== SCRIPT FILES =========-->
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    }
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/popper.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/waves.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.slimscroll.js')}}"></script>
@yield('page-js')
<script type="text/javascript" src="{{ asset('assets/js/pcoded.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/script.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/vertical-layout.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/jquery.bootstrap-growl.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('assets/js/custom.js')}}"></script>
<script>
    if (window.location.hash && window.location.hash == '#_=_') {
        window.location = '';
    }
    $("[data-toggle='tooltip']").click(function () {
        var $this = $(this);
        $(".tooltip").fadeOut("fast", function () {
            $this.blur();
        });
    });
    $(document).ready(function ($) {
        //Use this inside your document ready jQuery
        $(window).on('popstate', function () {
            location.reload(true);
        });
    });
    @if (Session::has('success'))
        $('.bootstrap-growl').remove();
        $.bootstrapGrowl("{{ Session::get('success') }}", { // options
            type: "success", // info, success, warning and danger
            ele: "body", // parent container
            offset: {
                from: "top",
                amount: 20
            },
            align: "right", // right, left or center
            width: 300,
            delay: 4000,
            allow_dismiss: true, // add a close button to the message
            stackup_spacing: 10
        });
    @endif
    @if (Session::has('error'))
        $('.bootstrap-growl').remove();
        $.bootstrapGrowl("{{ Session::get('error') }}", { // options
            type: "danger", // info, success, warning and danger
            ele: "body", // parent container
            offset: {
                from: "top",
                amount: 20
            },
            align: "right", // right, left or center
            width: 300,
            delay: 4000,
            allow_dismiss: true, // add a close button to the message
            stackup_spacing: 10
        });
    @endif
</script>
</body>
</html>
