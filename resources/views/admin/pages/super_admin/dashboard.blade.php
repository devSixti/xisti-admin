@extends('admin.layout.super_admin')
@section('title', __('admin.nav.dashboard'))
@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/css/widget/widget.css') }}">
    <style>
        .sos-st-card h5:after {
            width: 0;
        }
        .sos-st-card.green h3, .sos-st-card.green h5 {
            color: #2ed8b6;
        }
        .sos-st-card.purple h3, .sos-st-card.purple h5 {
            color: #b881e6;
        }
        .sos-st-card.blue h3, .sos-st-card.blue h5 {
            color: #4099ff;
        }
        .sos-st-card.yellow h3, .sos-st-card.yellow h5 {
            color: #FFB64D;
        }
        .sos-st-card.red h3, .sos-st-card.red h5 {
            color: #FF5370;
        }
        .services .col-xl-3 {
            max-width: 20%;
        }
        .services .card-block {
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .services h3 {
            font-size: 10px;
            font-weight: bold;
        }
        .services h5 {
            padding-top: 5px;
            font-size: 16px;
        }
        .services .col-auto {
            padding: 0;
            padding-left: 5px;
        }
        .services .text-right {
            padding: 0 5px;
            padding-left: 7px;
        }
        .services .text-right {
            display: inline-block;
        }
        .sos-st-card h5:after {
            width: 0;
        }
        .latest-update-card .card-block .latest-update-box:after {
            width: 0;
        }
        .latest-update-box p {
            margin-bottom: .5rem;
        }
        .latest-update-box p span.offers {
            font-size: 16px;
            font-weight: 500;
            font-family: "Quicksand", sans-serif;
        }
        .dashboard-box {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 5px 0 rgba(43, 43, 43, 0.1), 0 2px 6px -7px rgba(43, 43, 43, 0.1) !important;
        }
        .dashboard-title {
            padding: 1rem !important;
        }
        .dashboard-title h5 {
            font-size: 16px;
        }
        .order-list td, .table th {
            padding: 0.7rem 0.75rem !important;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-home bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.nav.dashboard') }}</h5>
                            <span>{{ __('admin.dashboard.summary') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pcoded-inner-content">
            <div class="main-body">
                <div class="page-wrapper">
                    <div class="page-body">

                        <div class="row">
                            <div class="col-xl-12 col-md-12">
                                <div class="card dashboard-box">
                                    <div class="card-block dashboard-title">
                                        <h5>{{ __('admin.dashboard.today_summary') }}</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- product profit start -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-blue">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30" style="">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">{{ __('admin.dashboard.total_revenue') }}</h6>
                                                <h3 class="m-b-0 f-w-700 text-white ">
                                                    <span class="currency"></span>
                                                    {{ isset($total_revenue) ? $total_revenue : 0 }}
                                                </h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fa fa-money text-c-blue f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-green">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">{{ __('admin.dashboard.complete_rides') }}</h6>
                                                <h3 class="m-b-0 f-w-700 text-white">{{ isset($total_completed_order) ? $total_completed_order : 0 }}</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-clipboard-check text-c-green f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-red">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">{{ __('admin.dashboard.cancelled_rides') }}</h6>
                                                <h3 class="m-b-0 f-w-700 text-white">{{ isset($total_cancelled_order) ? $total_cancelled_order : 0 }}</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-cart-arrow-down text-c-red f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card prod-p-card card-yellow">
                                    <div class="card-body">
                                        <div class="row align-items-center m-b-30">
                                            <div class="col">
                                                <h6 class="m-b-5 text-white">{{ __('admin.dashboard.total_rides') }}</h6>
                                                <h3 class="m-b-0 f-w-700 text-white">{{ isset($total_order) ? $total_order : 0 }}</h3>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fa fa-tags text-c-yellow f-18"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- product profit end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="styleSelector"></div>
@endsection
@section('page-js')
    <script type="text/javascript" src="{{ asset('assets/js/chart/jquery.flot.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/chart/amcharts.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/chart/serial.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/chart/light.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/chart/custom-dashboard.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
        });
    </script>
@endsection

