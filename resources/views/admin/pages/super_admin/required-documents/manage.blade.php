@extends('admin.layout.super_admin')
@section('title')
    Required Documents List
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        .action a {
            /*margin: 0;*/
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ other service horizontal navbar ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-list bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>Required Documents List</h5>
                                    <span>All Required Documents List
                                    </span>
                                </div>
                            </div>
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
                                <h5>Required Documents
                                    List</h5>
                                <a href="{{ route('get:admin:add_required_document') }}"
                                   class="btn btn-success m-b-0 btn-right render_link">Add Document</a>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="new-cons" class="table table-striped table-bordered nowrap"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th style="width: 30px;">No</th>
                                            <th>Name</th>
                                            <th data-orderable="false">Contains Expiry</th>
                                            <th data-orderable="false" style="width: 50px;">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($required_documents_list))
                                            @foreach($required_documents_list as $key => $required_document)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        {{ ucwords($required_document->document_name) }}
                                                    </td>
                                                    {{--                                                    Document Expiry--}}
                                                    <td>
                                                        <span class="toggle">
                                                                <label>
                                                                    <input name="contains_expiry"
                                                                           class="form-control contains_expiry"
                                                                           id="documents_id_{{$required_document->id}}"
                                                                           documents_id="{{$required_document->id}}"
                                                                           contains_expiry="{{$required_document->contains_expiry}}"
                                                                           type="checkbox" {{ ("1" == $required_document->contains_expiry) ? 'checked' : '' }}>
                                                                    <span class="button-indecator" data-toggle="tooltip"
                                                                          data-placement="top"
                                                                          title="{{ ("1" == $required_document->contains_expiry) ? 'Active' : 'InActive' }}"></span>
                                                                </label>
                                                            </span>
                                                    </td>
                                                    <td class="action">

                                                        <span class="toggle">
                                                            <label>
                                                                <input name="required_document"
                                                                       class="form-control document_status"
                                                                       id="document_id_{{$required_document->id}}"
                                                                       document_id="{{$required_document->id}}"
                                                                       document_status="{{$required_document->status}}"
                                                                       type="checkbox" {{ ("1" == $required_document->status) ? 'checked' : '' }}>
                                                                <span class="button-indecator" data-toggle="tooltip"
                                                                      data-placement="top"
                                                                      title="{{ ("1" == $required_document->status) ? 'Active' : 'InActive' }}"></span>
                                                            </label>
                                                        </span>
                                                        <a href="{{ route('get:admin:edit_required_document',['id' => $required_document->id]) }}"
                                                           class="render_link">
                                                            <img src="{{ asset('/assets/images/template-images/writing-1.png') }}"
                                                                 style="width:20px; height: 20px;"
                                                                 data-toggle="tooltip" data-placement="top"
                                                                 title="Edit">
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
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

    {{--User Delete Script--}}
    <script type="text/javascript">
        $(document).on('click', '.document_status', function (e) {
            e.preventDefault();
            var id = $(this).attr('document_id');
            var status = $(this).attr('document_status');
            var txt, title;
            if (status == 1) {
                title = "Disable Document?";
                txt = "if press yes then disable document!";
            }
            else {
                title = "Enable Document?";
                txt = "if press yes then enable Document!";
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
                            url: '{{ route("get:ajax:admin:update_required_document_status") }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    var document_id = '#document_id_' + id;
                                    if (result.status == 1) {
                                        $(document_id).prop("checked", true);
                                        $(document_id).attr("document_status", 1);
                                        swal("Success", "Enable Document Successfully", "success");
                                    }
                                    else {
                                        $(document_id).prop("checked", false);
                                        $(document_id).attr("document_status", 0);
                                        swal("Success", "Disable Document Successfully", "success");
                                    }
                                }else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        if (status == 1) {
                            swal("Cancelled", "Document is Enable", "error");
                        }
                        else {
                            swal("Cancelled", "Document is Disable", "error");
                        }
                    }
                });
        });

        //for contains Expiry
        $(document).on('click', '.contains_expiry', function (e) {
            e.preventDefault();
            var id = $(this).attr('documents_id');
            var contains_expiry = $(this).attr('contains_expiry');
            var txt, title;
            if (contains_expiry == 1) {
                title = "Disable Document Expiry?";
                txt = "if press yes then disable document Expiry!";
            }
            else {
                title = "Enable Document Expiry?";
                txt = "if press yes then enable Document Expiry!";
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
                            url: '{{ route("get:ajax:admin:update_document_expiry_status") }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    var documents_id = '#documents_id_' + id;
                                    if (result.contains_expiry == 1) {
                                        $(documents_id).prop("checked", true);
                                        $(documents_id).attr("contains_expiry", 1);
                                        swal("Success", "Enable Document Expiry Successfully", "success");
                                    }
                                    else {
                                        $(documents_id).prop("checked", false);
                                        $(documents_id).attr("contains_expiry", 0);
                                        swal("Success", "Disable Document Expiry Successfully", "success");
                                    }
                                }else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        if (contains_expiry == 1) {
                            swal("Cancelled", "Document Expiry is Enable", "error");
                        }
                        else {
                            swal("Cancelled", "Document Expiry is Disable", "error");
                        }
                    }
                });
        });

    </script>
@endsection
