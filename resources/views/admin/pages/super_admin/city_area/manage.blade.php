@extends('admin.layout.super_admin')
@section('title', __('admin.pages.city_area_list'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
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
                                    <h5>{{ __('admin.pages.city_area_list') }}</h5>
                                    <span>{{ __('admin.pages.all_city_area') }}</span>
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
                    <div class="page-body">
                        <div class="card">
                            <div class="card-header">
                                <h5>City Area</h5>
                                <a href="{{ route('get:admin:add_city_area') }}"
                                   class="btn btn-primary m-b-0 btn-right render_link">Add City Area</a>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="new-cons" class="table table-striped table-bordered nowrap"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>{{ __('admin.common.no') }}</th>
                                            <th>Area</th>
                                            <th>{{ __('admin.common.status') }}</th>
                                            <th>{{ __('admin.common.actions') }}</th>
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
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#new-cons').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("get:ajax:admin:city_area_list") }}', // Replace with the actual route
                    type: 'GET',
                },
                columns: [
                    {
                        data: 'no',
                        title: 'No',
                        orderable: false,
                    },
                    {
                        data: 'name',
                        title: 'Area',
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        orderable: false,
                        render: function (data, type, row) {
                            return `<span class="toggle">
                                <label>
                                    <input name="status"
                                           class="form-control area"
                                           id="area_id_${row.id}"
                                           area_id="${row.id}"
                                           area_status="${data}"
                                           type="checkbox" ${data == 1 ? 'checked' : ''}>
                                    <span class="button-indecator" data-toggle="tooltip"
                                          data-placement="top"
                                          id="title_status_${row.id}"
                                          title="${data == 1 ? 'Active' : 'Inactive'}"></span>
                                </label>
                            </span>`;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        title: 'Actions',
                        render: function (data, type, row) {
                            const editUrl = `{{ route('get:admin:edit_city_area', ['id' => ':id']) }}`.replace(':id', row.id);
                            return `<a class="render_link" href="${editUrl}">
                                <img src="/assets/images/template-images/writing-1.png" style="width:20px; height:20px;" data-toggle="tooltip" data-placement="top" title="Edit">
                            </a>`;
                        }
                    },
                ],
                order: [[0, 'desc']] // Default sorting by Area
            });

            // var table = $('#new-cons').DataTable();
            $(document).on('click', '.delete', function (e) {
                e.preventDefault();
                var id = $(this).attr('areaid');
                var RemovetableRow = table.row($(this).parents('tr'));
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
                                url: '{{ route('get:admin:delete_city_area') }}',
                                data: {id: id},
                                success: function (result) {
                                    if (result.success == true) {
                                        var new_id = "#delete_customer_" + id;
                                        swal("Success", "City Area remove successfully", "success");
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
        });
        $(document).on('click', '.area', function (e) {
            e.preventDefault();
            var id = $(this).attr('area_id');
            var status = $(this).attr('area_status');
            var txt, title;
            if (status == 1) {
                title = "Disable area?";
                txt = "if press yes then disable area!";
            } else {
                title = "Enable area?";
                txt = "if press yes then enable area!";
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
                            url: '{{ route("get:admin:update_city_area_status") }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    var area_id = '#area_id_' + id;
                                    var title_status = '#title_status_' + id;
                                    if (result.status == 1) {
                                        $(area_id).prop("checked", true);
                                        $(area_id).attr("area_status", 1);
                                        swal("Success", "Enable Area successfully", "success");
                                    } else {
                                        $(area_id).prop("checked", false);
                                        $(area_id).attr("area_status", 0);
                                        swal("Success", "Disable Area successfully", "success");
                                    }
                                } else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        if (status == 1) {
                            swal("Cancelled", "Area is Enable", "error");
                        } else {
                            swal("Cancelled", "Area is Disable", "error");
                        }
                    }
                });
        });
    </script>
@endsection

