@extends('admin.layout.super_admin')
@section('title')
    @if(isset($status) && $status == 'scheduled' )
        @if(isset($slug) && $slug == "courier-service") Services @else Scheduled Ride @endif
    @elseif(isset($status) && $status == 'all')
        @if(isset($slug) && $slug == "courier-service") All Services @else All Ride @endif
    @elseif(isset($status) && $status == "pending")
        @if(isset($slug) && $slug == "courier-service")  Pending Services @else Pending Ride @endif
    @elseif(isset($status) && $status == "cancelled")
        Cancelled Ride
    @elseif(isset($status) && $status == "approved")
        Approved Ride
    @elseif(isset($status) && $status == "ongoing")
        Ongoing Ride
    @elseif(isset($status) && $status == "completed")
        Completed Ride
    @endif
    List
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <!-- Data Table Excel Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/responsive/buttons.dataTables.min.css?v=0.1')}}">
    <style>
        table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before, table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before {
            background: #55d090;
        }

        .page-item.active .page-link {
            background: #55d090;
            border-color: #55d090;
        }

        /*datatable td link*/
        .ride-status a {
            color: #55d090;
            font-weight: bold;
            font-size: 14px;
        }

        .ride-status i {
            display: inline-block;
            font-size: 20px;
        }

        .icon-list-demo i {
            height: auto;
            line-height: 10px;
            border: none;
            margin-right: 5px;
            color: #55d090;
            width: unset;
        }

        .page-link {
            color: #55d090;
        }
        .top {
            display: flex;
        }
        .dataTables_filter {
            margin-left: auto;
        }
        .dt-buttons {
            margin-left: 1em;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-list bg-c-green"></i>
                        <div class="d-inline">
                            <h5>@if(isset($status) && $status == 'scheduled' )
                                    Scheduled Ride
                                @elseif(isset($status) && $status == 'all')
                                    All Ride
                                @elseif(isset($status) && $status == "pending")
                                     Pending Ride
                                @elseif(isset($status) && $status == 'cancelled')
                                   Cancelled Ride
                                @elseif(isset($status) && $status == "approved")
                                    Approved Ride
                                @elseif(isset($status) && $status == "ongoing")
                                    Ongoing Ride
                                @elseif(isset($status) && $status == "completed")
                                    Completed Ride
                                @endif
                                List
                                @if(isset($driver_details) && $driver_details != Null) of {{ ucwords(strtolower($driver_details->driver_name)) }} @endif
                            </h5>
                            <span>All
                                @if(isset($status) && $status == 'scheduled' )
                                     Scheduled Ride
                                @elseif(isset($status) && $status == 'all')
                                     Ride
                                @elseif(isset($status) && $status == "pending")
                                     Pending Ride
                                @elseif(isset($status) && $status == 'cancelled')
                                   Cancelled Ride
                                @elseif(isset($status) && $status == "approved")
                                    Approved Ride
                                @elseif(isset($status) && $status == "ongoing")
                                    Ongoing Ride
                                @elseif(isset($status) && $status == "completed")
                                    Completed Ride
                                @endif
                                List
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
                        <div class="card">
                            <div class="card-header">
                                <h5>@if(isset($status) && $status == 'scheduled' )
                                        Scheduled Ride
                                    @elseif(isset($status) && $status == 'all')
                                        All Ride
                                    @elseif(isset($status) && $status == "pending")
                                       Pending Ride
                                    @elseif(isset($status) && $status == 'cancelled')
                                       Cancelled Ride
                                    @elseif(isset($status) && $status == "approved")
                                        Approved Ride
                                    @elseif(isset($status) && $status == "ongoing")
                                        Ongoing Ride
                                    @elseif(isset($status) && $status == "completed")
                                        Completed Ride
                                    @endif
                                    List
                                    @if(isset($driver_details) && $driver_details != Null) - {{ ucwords(strtolower($driver_details->driver_name)) }} @endif
                                </h5>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="rides" class="table table-striped table-bordered nowrap" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th id="no">No</th>
                                            <th>@if(isset($slug) && $slug == "courier-service") Delivery No. @else Ride No. @endif</th>
                                            <th>@if(isset($slug) && $slug == "courier-service") Recipient Name @else Customer Name @endif</th>
                                            <th>Driver Name</th>
                                            <th>Vehicle Type</th>
                                            <th>Total Cost</th>
                                            <th>Status</th>
                                            @if(Illuminate\Support\Facades\Auth::guard("admin")->check() && isset($slug) && $slug == "courier-service")
                                                <th>Payment Status</th>
                                                <th>Refund Status</th>
                                            @endif
                                            <th>Chat</th>
                                            <th>Details</th>
                                            <th>@if(isset($slug) && $slug == "courier-service") Pick / Delivery @else Pick / Drop @endif Address</th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
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
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}" type="text/javascript"></script>
    <!-- CDN for the Excel file -->
    <script src="{{asset('assets/js/responsive/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/jszip.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/datatablecommonfunction.js')}}"></script>

    <script>
        $(document).ready(function () {
            var table = $('#rides').DataTable({
                processing: true,
                language : {
                    loadingRecords : '&nbsp;',
                    {{--processing: "<img src='{{ asset('assets/images/website-logo-icon/loader.gif') }}' />",--}}
                    processing: "<img src='{{ asset('/assets/images/website-logo-icon/loader.gif')}}' style='width: 50px; height: 50px;' />",
                    // processing: "<img src='http://127.0.0.1:8000/assets/images/website-logo-icon/loader.gif'  style='width: 10px; height: 10px;'/>",
                    // processing: "<img src='http://127.0.0.1:8000/assets/images/website-logo-icon/loader.gif' />"
                },
                serverSide: true,
                pageLength: 25,
                responsive: true,
                ajax: {
                    url: "{{route('get:admin:ride_list_new')}}",
                    data: {status_check : "{{ $status_check }}"}
                },
                columns: [
                    {data: 'no'},
                    {data: 'ride_no'},
                    {data: 'user_name'},
                    {data: 'driver_name'},
                    {data: 'vehicle_name'},
                    {data: 'total_pay'},
                    {data: 'status', 'sortable': false},
                    @if(Illuminate\Support\Facades\Auth::guard("admin")->check() && isset($slug) && $slug == "courier-service")
                        {data: 'payment_status'},
                        {data: 'refund_status'},
                    @endif
                    {data: 'chat', 'sortable': false},
                    {data: 'details', 'sortable': false},
                    {data: 'address', 'sortable': false},
                ],
                dom: '<"top"lBf>rt<"bottom"pi><"clear">',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.notexport)',
                        modifier: {
                            page: 'all',
                        },
                    },
                    text: 'Download Excel',
                    "action": newexportaction,
                }],
            });
        });
    </script>
@endsection
