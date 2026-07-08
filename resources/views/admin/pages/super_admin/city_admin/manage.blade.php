@extends('admin.layout.super_admin')
@section('title', __('admin.pages.admin_list'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        table th, table td {
            word-wrap: break-word !important;
            white-space: normal;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">

        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-list bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('admin.pages.admin_list') }}</h5>
                            <span>{{ __('admin.pages.all_admin_list') }}</span>
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
                                <h5>City Admin List</h5>
                                <a href="{{ route('get:admin:add_city_admin') }}" class="btn btn-primary m-b-0 btn-right render_link">Add City Admin</a>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="new-cons" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th style="width: 30px !important;">{{ __('admin.common.no') }}</th>
                                            <th style="width:600px !important;">{{ __('admin.columns.name') }}</th>
                                            <th style="width:600px !important;">{{ __('admin.columns.email') }}</th>
                                            <th style="width: 60px !important;">{{ __('admin.common.actions') }}</th>
                                        </tr>
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
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}" type="text/javascript"></script>

        {{--    city admin datatable ajax code --}}
    <script type="text/javascript">
        var newcs = $('#new-cons').DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            pageLength: 20,
            lengthMenu: [10, 20, 50, 100],
            ajax: "{{route('get:admin:city_admin_list_new')}}",
            columns: [
                { data: 'id', sortable: false },
                { data: 'name' },
                { data: 'email' },
                { data: 'action'},
            ],
            order: [[1, 'desc']], // Default order by currency name
        });
    </script>
    <script>
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var id = $(this).attr('adminid');
            swal({
                    title: "Sub admin Remove?",
                    text: "if press yes then sub admin is remove!",
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
                            async : false,
                            url: '{{ route('get:admin:delete_city_admin') }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
//                                    location.reload();
                                    var new_id = "#hide_" + id;
                                    swal("Success", "Page remove successfully", "success");
                                    $(new_id).hide();
                                }else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "Page not removed", "error");
                    }
                });
        });
    </script>
@endsection
