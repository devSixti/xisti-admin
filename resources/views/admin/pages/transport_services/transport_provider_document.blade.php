@extends('admin.layout.super_admin')
@section('title', __('admin.pages.driver_required_documents'))
@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/css/widget/widget.css') }}">
    <style>
        .comp-card i {
            width: 35px !important;
            height: 35px !important;
            padding: 8px 0 !important;
        }

       /*sweetalert icon color*/
        div:where(.swal2-icon).swal2-warning {
            border-color: #2ed8b6 !important;
            color: #2ed8b6  !important;
        }

        .comp-card:hover i {
            border-radius: 5px !important;
        }

        .comp-card i:hover {
            border-radius: 50% !important;
        }
        .preview {
            display: block;
            height: 150px;
            width: 40%;
            margin: 10px auto 20px;
            border: 1px solid gainsboro;
            /*text-align: center;*/
        }

        #fileid {
            display: none;
        }

        .preview span {
            display: block;
            text-align: center;
            width: 80%;
            margin: 15px auto;
            border: none;
        }
    </style>
    <style>
        #document-expiry {
            position: relative;
            width: 150px; height: 40px;
            color: white;
        }

        #document-expiry:before {
            position: absolute;
            top: 3px; left: 3px;
            content: attr(data-date);
            display: inline-block;
            color: black;
        }

        input::-webkit-datetime-edit, input::-webkit-inner-spin-button, input::-webkit-clear-button {
            display: none;
        }

        input::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 3px;
            right: 0;
            color: black;
            opacity: 1;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ other service horizontal navbar ] start -->
        <div class="other-service-horizontal-nav">
            {{--            @include('admin.include.transport-horizontal-navbar')--}}
        </div>
        <!-- [ other service horizontal navbar ] end -->
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-list bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>{{ __('admin.pages.driver_required_documents') }}</h5>
                                    <span>{{ __('admin.forms.driver_required_documents_list') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            {{--<a href="{{ route('get:transport:transport_provider_list') }}"--}}
                            {{--class="btn btn-primary m-b-0 btn-right render_link">{{ __('admin.common.back') }}</a>--}}
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
                        <div class="row">
                            <!-- analytic card start -->
                            @if(isset($required_documents_lists) && !$required_documents_lists->isEmpty())
                                @foreach($required_documents_lists as $key => $required_document)
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card comp-card">
                                            <div class="card-body text-center">
                                                <h6 class="m-b-20">{{ ucwords($required_document->name) }}</h6>
                                                @if($provider_documents[$key] == Null)
                                                    <p>{{ $required_document->name }} not found</p>
                                                @else
                                                    @if((isset($provider_documents[$key])) && ($provider_documents[$key]->document_file != Null) && file_exists(public_path('/assets/images/provider-documents/'.$provider_documents[$key]->document_file)))
                                                        @if($provider_documents[$key]->status == 0)
                                                            <p class="provider_document_id_{{ $provider_documents[$key]->id }}">
                                                                Pending</p>
                                                        @elseif($provider_documents[$key]->status == 1)
                                                            <p class="provider_document_id_{{ $provider_documents[$key]->id }}"
                                                               id="approved_id_{{ $provider_documents[$key]->id }}">
                                                                Approved</p>
                                                        @elseif($provider_documents[$key]->status == 2)
                                                            <p class="provider_document_id_{{ $provider_documents[$key]->id }}"
                                                               id="reject_id_{{ $provider_documents[$key]->id }}">
                                                                Rejected</p>
                                                        @endif
                                                        @if($required_document->contains_expiry == 1)
                                                            <p class="provider_document_id_{{ $provider_documents[$key]->id }} text-danger"
                                                               id="reject_id_{{ $provider_documents[$key]->id }}">{{isset($provider_documents[$key]) ? ($provider_documents[$key]->expiry_date != null ? "Expires on ". \Carbon\Carbon::parse(($provider_documents[$key]->expiry_date))->format('d-m-Y') : "") : ""}}</p>
                                                        @endif
                                                    @else
                                                        <p>{{ $required_document->name }} not found</p>
                                                    @endif
                                                @endif
                                                <input type="hidden" id="document-{{ $key }}" value="{{ (isset($provider_documents[$key]) && (file_exists(public_path('/assets/images/provider-documents/'.$provider_documents[$key]->document_file)))) ? (asset('assets/images/provider-documents/'.$provider_documents[$key]->document_file)) : "" }}">
                                                <a class="open-form" data-toggle="modal"
                                                   data-target="#open" slug="{{ $required_document->id }}"
                                                   doc_name="{{ $required_document->name }}"
                                                   id="{{ $key }}"
                                                   doc-expiry="{{isset($provider_documents[$key]) ? \Carbon\Carbon::parse(($provider_documents[$key]->expiry_date))->format('d-m-Y') : ""}}"
                                                >
                                                    <i class="fas fa-upload bg-c-blue document_view"
                                                       data-toggle="tooltip"
                                                       data-placement="top" title="Upload"></i>
                                                    {{--                                                    {{ ucwords($required_document->name) }}--}}
                                                </a>
                                                @if($required_document->contains_expiry == 1)
                                                    @if((isset($provider_documents[$key])) && ($provider_documents[$key]->document_file != Null) && file_exists(public_path('/assets/images/provider-documents/'.$provider_documents[$key]->document_file)))
                                                        <a class="open-form" data-toggle="modal" data-target="#open_date" slug="{{ $required_document->id }}" doc_name="{{ $required_document->name }}" id="{{ $key }}" doc-expiry="{{isset($provider_documents[$key]) ? \Carbon\Carbon::parse(($provider_documents[$key]->expiry_date))->format('d-m-Y') : ""}}" >
                                                            <i class="fas fa-calendar-alt bg-c-blue document_view" data-toggle="tooltip" data-placement="top" title="Date"></i>
                                                            {{-- {{ ucwords($required_document->name) }}--}}
                                                        </a>
                                                    @else
                                                        <a class="open-form" data-toggle="modal" data-target="#" disabled onclick="uploadDocumentAlert()">
                                                            <i class="fas fa-calendar-alt bg-c-blue document_view" data-toggle="tooltip" data-placement="top" title="date"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                                @if((isset($provider_documents[$key])) && ($provider_documents[$key]->document_file != Null) && file_exists(public_path('assets/images/provider-documents/'.$provider_documents[$key]->document_file)))
                                                    <a href="{{ asset('assets/images/provider-documents/'.$provider_documents[$key]->document_file) }}"
                                                       target="_blank">
                                                        <i class="fa fa-eye bg-c-blue document_view"
                                                           data-toggle="tooltip" data-placement="top" title="View"></i>
                                                    </a>
                                                @else
                                                    <a>
                                                        <i class="fa fa-eye-slash bg-c-blue" data-toggle="tooltip"
                                                           data-placement="top" title="View"></i>
                                                    </a>
                                                @endif
                                                <a class="document_status"
                                                   id="document_approved_id_{{ isset($provider_documents[$key])? $provider_documents[$key]->id : ''}}"
                                                   document_approved_id="{{ isset($provider_documents[$key])? $provider_documents[$key]->id : ''}}"
                                                   document_status="1">
                                                    <i class="fa fa-thumbs-up bg-c-green" data-toggle="tooltip"
                                                       data-placement="top" title="Approve"></i>
                                                </a>
                                                <a class="document_status"
                                                   id="document_approved_id_{{ isset($provider_documents[$key])? $provider_documents[$key]->id : ''}}"
                                                   document_approved_id="{{ isset($provider_documents[$key])? $provider_documents[$key]->id : ''}}"
                                                   document_status="2">
                                                    <i class="fa fa-thumbs-down bg-c-red" data-toggle="tooltip"
                                                       data-placement="top" title="Reject"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{--                                <div class="col-xl-3 col-md-6">--}}
                                {{--                                    <div class="card comp-card">--}}
                                {{--                                        <div class="card-body text-center">--}}
                                {{--                                            --}}{{--<h6 class="m-b-20">{{ __('admin.modules.required_documents') }}</h6>--}}
                                {{--                                            <p>Required documents list not found</p>--}}
                                {{--                                            <a>--}}
                                {{--                                                <i class="fa fa-eye bg-c-blue"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                            <a class="document_status">--}}
                                {{--                                                <i class="fa fa-thumbs-up bg-c-green"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                            <a class="document_status">--}}
                                {{--                                                <i class="fa fa-thumbs-down bg-c-red"></i>--}}
                                {{--                                            </a>--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                <div class="page-wrapper ml-auto mr-auto">
                                    <h4 class="">{{ __('admin.forms.no_documents_required') }}</h4>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>
        <div class="modal fade" id="open" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" class="document-upload-form"
                          action="{{ route('post:admin:transport_upload_provider_document') }}"
                          enctype="multipart/form-data">
                        {{csrf_field() }}
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{isset($user_id) ? $user_id : 0}}" id="user_id">
                            <input type="hidden" name="doc_id" value="" id="doc_id">
                            <div class="preview">
                                <span>
                                <img id="upload-preview" src="">
                                </span>
                            </div>
                            <input id='fileid' name="document_file" type='file' />
                            <span class="error d-none file-error">{{ __('admin.forms.document_file_required') }}</span>
                            {{--                            <center>--}}
                            {{--                                <input type='date' id='document-expiry' name="document_expiry" class="form-control text-dark w-50 mb-3" placeholder="{{ __('admin.forms.enter_expiry_date_lowercase') }}" />--}}
                            {{--                                <span class="error d-none date-error">Expiry date is required</span>--}}
                            {{--                            </center>--}}
                            <center>
                                <input id='buttonid' class="btn btn-success" type='button' value="Upload Document"/>
                            </center>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary doc-save">{{ __('admin.common.save') }}</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Date Model -->
        <div class="modal fade" id="open_date" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form method="post" class="document-upload-form"
                          action="{{ route('post:admin:transport_update_provider_document_expiry') }}"
                          enctype="multipart/form-data">
                        {{csrf_field() }}
                        <div class="modal-body">
                            <input type="hidden" name="user_id" value="{{isset($user_id) ? $user_id : 0}}" id="user_id">
                            <input type="hidden" name="docs_id" value="" id="docs_id">
{{--                            <input id='fileid' name="document_file" type='file' />--}}
                            <center>
                                <input type='date' data-date="" data-date-format="DD-MM-YYYY" id='document-expiry' name="document_expiry" class="form-control text-dark w-50 mb-3" placeholder="{{ __('admin.forms.enter_expiry_date_lowercase') }}" onfocus="disablePastDates()" />
                            </center>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary doc-save">{{ __('admin.common.save') }}</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script type="text/javascript" src="{{ asset('/assets/js/widget/jquery.wavify.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/widget/TweenMax.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/widget/widget-statistic.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/assets/js/moment.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"  crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function uploadDocumentAlert() {
            Swal.fire({
                title: 'Upload Document',
                text: 'Please upload the Document First.',
                icon: 'warning',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-success',
                    icon: 'text-success'
                }
            });
        }

        function disablePastDates() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            // MAX date (today + 30 years)
            var maxDate = new Date(today);
            maxDate.setFullYear(maxDate.getFullYear() + 30);
            var maxDd = String(maxDate.getDate()).padStart(2, '0');
            var maxMm = String(maxDate.getMonth() + 1).padStart(2, '0');
            var maxYyyy = maxDate.getFullYear();
            var max = maxYyyy + '-' + maxMm + '-' + maxDd;

            today = yyyy + '-' + mm + '-' + dd;
            document.getElementById("document-expiry").setAttribute("min", today);
            document.getElementById("document-expiry").setAttribute("max", max);
        }

        function setdateFormat(event) {
            console.clear()
            let d = new Date(event.value);
            console.log(d)
            let dt = d.getDate();
            let mn = d.getMonth();
            mn++;
            let yy = d.getFullYear();
            console.log(yy)
            if (mn < 10){
                mn = '0' + mn;
            }
            if (dt < 10){
                dt = '0' + dt;
            }
            document.getElementById("document-expiry").value = yy + "-" + mn + "-" + dt;
        }

        $("#document-expiry").on("change", function() {
            console.clear();
            this.setAttribute(
                "data-date",
                moment(this.value, "YYYY-MM-DD")
                    .format( this.getAttribute("data-date-format") )
            )
        }).trigger("change")

        document.getElementById('buttonid').addEventListener('click', openDialog);

        function openDialog() {
            document.getElementById('fileid').click();

        }
        var imgtag = "#upload-preview";


        $(document).on('click', '.document_status', function (e) {
            e.preventDefault();
            var id = $(this).attr('document_approved_id');
            var status = $(this).attr('document_status');
            var txt, title;
            console.log(id);
            if (status == 1) {
                title = "Approved Document File?";
                txt = "if press yes then the document will be approved!";
            }
            else if (status == 2) {
                title = "Reject Document File?";
                txt = "if press yes then document will be rejected!";
            }
            if (id.trim() != "") {
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
                                url: '{{ route("get:ajax:admin:update_approved_reject_provider_document") }}',
                                data: {id: id, status: status},
                                success: function (result) {
                                    if (result.success == true) {
                                        var provider_document_id = '.provider_document_id_' + id;
                                        if (result.status['status'] == 1) {
                                            $(provider_document_id).empty();
                                            $(provider_document_id).text(@json(__('admin.status.approved')));
                                            swal("Success", "Approved Document File Successfully", "success");
                                            location.reload();
                                        }
                                        else if (result.status['status'] == 2) {
                                            $(provider_document_id).empty();
                                            $(provider_document_id).text(@json(__('admin.status.rejected')));
                                            swal("Success", "Reject Document File Successfully", "success");
                                            location.reload();
                                        } else {
                                            $(provider_document_id).empty();
                                            $(provider_document_id).text(@json(__('admin.status.pending')));
                                            swal("Success", "Reject Document File Successfully", "success");
                                        }
                                    }else {
                                        swal("Warning", result.message, "warning");
                                    }
                                }
                            })
                        } else {
                            if (status == 1) {
                                swal("Cancelled", "Your action is cancelled !", "error");
                            }
                            else {
                                swal("Cancelled", "Your action is cancelled !", "error");
                            }
                        }
                    });
            }
        });

        $('.open-form').on('click', function () {
            let docExp = $('#document-expiry');
            $("#fileid").val("");

            $(".file-error").addClass('d-none');
            $(".date-error").addClass('d-none');

            $(imgtag).attr('src','');
            var key = $(this).attr('id');
            var slug = $(this).attr('slug');
            console.log(slug);
            var name = $(this).attr('doc_name');
            $('.slug').val(slug);
            $('#doc_id').val(slug);
            $('#docs_id').val(slug);
            $('.modal-title').text(name);
            docExp.val($(this).attr('doc-expiry'));
            docExp.attr("data-date",
                moment($(this).attr('doc-expiry'), "DD-MM-YYYY")
                    .format(docExp.attr("data-date-format"))
            );

            var img_src = $('#document-' + key).val();
            if (img_src != "") {
                $(imgtag).show();
                $(imgtag).attr('src', img_src).css({
                    'width': "100%",
                    'height': "120px"
                });
            }
            else {
                $('#upload-preview').hide();
            }
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                let imageType;

                reader.onload = function (e) {
                    $(imgtag).show();
                    imageType = e.target.result
                    if ((imageType.split(":")[1].split("/")[0].toString()) === "image") {
                        return $(imgtag).attr('src', e.target.result).css({
                            'width': "100%",
                            'height': "120px"
                        });
                    }
                    imageType = (imageType.split(":")[1].split("/")[1].split(";")[0]).toString()
                    if (imageType === "pdf") {
                        $(imgtag).attr('src', `${window.location.origin}/assets/images/pdf_98_98.png`).css({
                            'width': "100%",
                            'height': "120px"
                        });
                    } else if (imageType === "vnd.openxmlformats-officedocument.wordprocessingml.document") {
                        $(imgtag).attr('src', `${window.location.origin}/assets/images/doc_128_128.png`).css({
                            'width': "100%",
                            'height': "120px"
                        });
                    } else if (imageType === "msword") {
                        $(imgtag).attr('src', `${window.location.origin}/assets/images/doc_128_128.png`).css({
                            'width': "100%",
                            'height': "120px"
                        });
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#fileid").change(function () {
            readURL(this);
        });

    </script>
@endsection



