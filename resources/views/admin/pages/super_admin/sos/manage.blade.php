@extends('admin.layout.super_admin')
@section('title', __('admin.pages.sos_list'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        .dataTables_wrapper .top {
            display: flex;
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
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-list bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>{{ __('admin.pages.sos_list') }}</h5>
                                    <span>{{ __('admin.pages.sos_list') }}</span>
                                </div>
                            </div>
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
                            <!-- Add SOS -->
                            <div class="card-header">
                                <h5>{{ __('admin.pages.sos_list') }}</h5>
                                <a href="{{ route('get:admin:add_sos') }}"
                                   class="btn btn-primary m-b-0 btn-right render_link">Add SOS</a>
                            </div>

                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="sos" class="table table-striped table-bordered nowrap"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>{{ __('admin.common.no') }}</th>
                                            <th>{{ __('admin.columns.name') }}</th>
                                            <th>Phone No.</th>
                                            <th>{{ __('admin.common.status') }}</th>
                                            <th>{{ __('admin.columns.action') }}</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
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
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}"></script>
    {{--Model Script type detials--}}
    <script src="{{ asset('assets/js/classie.js')}}" type="text/javascript"></script>
    <script rel="stylesheet" src="{{ asset('assets/js/validation/jquery.validate.js')}}"></script>

    <!-- JS for the Excel file -->
    <script src="{{asset('assets/js/responsive/dataTables.buttons.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/js/responsive/jszip.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/js/responsive/buttons.html5.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/js/responsive/buttons.print.min.js')}}" type="text/javascript"></script>

    <script>
        $(document).ready(function () {
            var table = $('#sos').DataTable({
                responsive: true,// Enable responsive behavior for better viewing on various screen sizes
                processing: true,// Show a processing indicator while loading data
                serverSide: true,// Enable server-side processing to handle large data sets more efficiently
                pageLength: 25,// Set default number of rows per page
                lengthMenu: [10, 25, 50, 100],// Define the options for rows per page dropdown menu
                ajax: "{{route('get:admin:sos_list')}}",
                columns: [
                    {data: 'id', sortable: false},
                    {data: 'name'},
                    {data: 'contact_number', sortable: false},
                    {data: 'status', sortable: false},
                    {data: 'action', sortable: false}
                ],
                "order": [[0, "desc"]],
                "createdRow": function (row, data) {
                    $(row).addClass('sos_delete_' + data.sos_id);
                }
            });
            //Delete SOS record
            $(document).on('click', '.delete', function (e) {
                e.preventDefault();
                var id = $(this).attr('sosid');
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this data!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: 'get',
                                async: false,
                                url: '{{ route('get:admin:delete_sos') }}',
                                data: {id: id},
                                success: function (result) {
                                    if (result.success == true) {
                                        $('#sos').DataTable().draw();
                                        var new_id = ".sos_delete_" + id;
                                        swal("Success", "SOS contact removed successfully!", "success");
                                        $(new_id).hide();
                                    } else {
                                        swal("Warning", result.message, "warning");
                                        console.log(result);
                                    }
                                }
                            })
                        } else {
                            swal("Cancelled", "Your Data is safe :)", "error");
                        }
                    });
            });
            // SOS status enable or disable
            $(document).on('click', '.sos_status', function (e) {
                e.preventDefault();
                var id = $(this).attr('sos_list_id');
                var status = $(this).attr('sos_status');
                var txt, title;
                if (status == 1) {
                    title = "Disable SOS Contact?";
                    txt = "if press yes then disable SOS contact!";
                } else {
                    title = "Enable SOS Contact?";
                    txt = "if press yes then enable SOS contact!";
                }
                swal({
                        title: title,
                        text: txt,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: 'get',
                                async: false,
                                url: '{{route("get:admin:update_sos_status")}}',
                                data: {id: id},
                                success: function (result) {
                                    if (result.success == true) {
                                        var sos_list_ = '#sos_list_' + id;
                                        if (result.status == 1) {
                                            $(sos_list_).prop("checked", true);
                                            $(sos_list_).attr("sos_status", 1);
                                            swal("Success", "SOS contact enabled successfully", "success");
                                            table.ajax.reload();
                                        } else {
                                            $(sos_list_).prop("checked", false);
                                            $(sos_list_).attr("sos_status", 0);
                                            swal("Success", "SOS contact disabled successfully", "success");
                                        }
                                    } else {
                                        swal("Warning", result.message, "warning");
                                        console.log(result);
                                    }
                                }
                            })
                        } else {
                            if (status == 1) {
                                swal("Cancelled", "SOS contact is Enable", "error");
                            } else {
                                swal("Cancelled", "SOS contact is Disable", "error");
                            }
                        }
                    });
            });
        });
    </script>
@endsection

