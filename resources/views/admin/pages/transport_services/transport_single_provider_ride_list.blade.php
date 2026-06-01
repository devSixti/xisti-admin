@extends('admin.layout.super_admin')
@section('title')
    All Ride List
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        /*datatable style*/
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
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
            @if(Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 1)
                <div class="other-service-horizontal-nav">
                    {{--@include('admin.include.transport-horizontal-navbar')--}}
                </div>
            @endif
        @endif

        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-list bg-c-green"></i>
                        <div class="d-inline">
                            <h5>All Ride List @if(isset($driver_details) && $driver_details != Null) - {{ ucwords(strtolower($driver_details->driver_name)) }} @endif
                            </h5>
                            <span>All Ride List</span>
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
                            <div class="card-block">
                                <div class="tab-content card-block">
                                    <div class="tab-pane @if( (isset($driver_type)) && $driver_type == 1) @else active show @endif" id="transport" role="tabpanel">
                                        <div class="dt-responsive table-responsive">
                                            <table id="service_transport" class="table table-striped table-bordered nowrap" style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Ride No.</th>
                                                    <th>Customer Name</th>
                                                    <th>Driver Name</th>
                                                    <th>Vehicle Type</th>
                                                    <th>Total Cost</th>
                                                    <th>Status</th>
                                                    <th>Details</th>
                                                    <th>Pick / Drop Address</th>
                                                </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('#service_transport').DataTable({
                processing: true,
                language : {
                    loadingRecords : '&nbsp;',
                    processing: "<img src='{{ asset('/assets/images/website-logo-icon/loader.gif')}}' style='width: 50px; height: 50px;' />",
                },
                serverSide: true,
                pageLength: 25,
                responsive: true,
                ajax: {
                    url: "{{route('get:admin:single_provider_ride_list_new')}}",
                    data: {
                        driver_id : "{{ $driver_id }}",
                    }
                },
                columns: [
                    {data: 'no'},
                    {data: 'ride_no'},
                    {data: 'user_name'},
                    {data: 'driver_name'},
                    {data: 'vehicle_name'},
                    {data: 'total_pay'},
                    {data: 'status', className : 'ride-status'},
                    {data: 'details', 'sortable': false},
                    {data: 'address', 'sortable': false},
                ]
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust()
                    .responsive.recalc();
            });
        });

    </script>
@endsection
