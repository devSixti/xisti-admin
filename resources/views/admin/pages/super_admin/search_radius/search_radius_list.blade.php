@extends('admin.layout.super_admin')
@section('title')
    Search Radius List
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        .btn-primary{
            color: #fff!important;
        }
        .md-modal {
            position: fixed;
            top: 30%;
            left: 35%;
            /*width: 50%;*/
            width: 30%;
            max-width: 630px;
            min-width: 300px;
            height: auto;
            z-index: 2000;
            visibility: hidden;
            -webkit-backface-visibility: hidden;
            -moz-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translateX(-50%) translateY(-50%);
            -moz-transform: translateX(-50%) translateY(-50%);
            -ms-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }
        .md-show {
            visibility: visible;
        }

        .md-overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            visibility: hidden;
            top: 0;
            left: 0;
            z-index: 1000;
            opacity: 0;
            background: rgba(55, 58, 60, 0.65);
            -webkit-transition: all 0.3s;
            -moz-transition: all 0.3s;
            transition: all 0.3s;
        }

        .md-show ~ .md-overlay {
            opacity: 1;
            visibility: visible;
        }

        /* Content styles */
        .md-content {
            color: #666666;
            background: #fff;
            position: relative;
            border-radius: 3px;
            margin: 0 auto;
        }

        .md-content h3 {
            color: #fff;
            margin: 0;
            /*padding: 0.4em;*/
            padding: 0.6em 0.4em 0.6em 1em;
            text-align: left;
            font-weight: 400;
            font-size: 1.5em;
            opacity: 0.8;
            border-radius: 3px 3px 0 0;
        }

        .md-content > div {
            padding: 15px 25px 30px 25px;
            margin: 0;
            font-size: 1em;
            /*font-weight: 300;*/
            /*font-size: 1.15em;*/
        }

        .md-content > div > div {
            /*width: 40%;*/
            width: 100%;
            margin: 0 auto;
            padding: 10px 0;
            justify-content: space-around;
            display: flex;
        }

        .md-content > div > div > img {
            border-radius: 50%;
            padding: 4px;
            border: 2px solid #2ed8b6;
        }

        .md-content > div ul {
            margin: 0;
            padding: 0 0 30px 0;
        }

        .md-content > div ul li {
            padding: 5px 0;
        }

        /* Individual modal styles with animations/transitions */
        .md-effect-1,.md-effect-2 .md-content {
            -webkit-transform: scale(0.7);
            -moz-transform: scale(0.7);
            -ms-transform: scale(0.7);
            transform: scale(0.7);
            opacity: 0;
            -webkit-transition: all 0.3s;
            -moz-transition: all 0.3s;
            transition: all 0.3s;
        }

        .md-show.md-effect-1,.md-show.md-effect-2 .md-content {
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            transform: scale(1);
            opacity: 1;
        }

        .md-trigger:hover {
            color: #64b0f2;
            cursor: pointer;
        }

        .md-trigger img:hover {
            opacity: 0.7;
            cursor: pointer;
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
                                    <h5> Search Radius List</h5>
                                    <span>All Search Radius List</span>
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
                            <div class="card-header">
                                <h5>Search Radius List</h5>
                                <a title="Add Search Radius"
                                   class="btn btn-primary m-b-0 btn-right add_search_radius">
                                    Add Search Radius</a>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="new-cons" class="table table-striped table-bordered nowrap"
                                           style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Radius</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if(isset($search_radius))
                                            @foreach($search_radius as $key => $radius)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        {{$radius->radius}} km
                                                    </td>
                                                    <td class="action">
                                                        <a class="render_link edit_search_radius" search_radius_id="{{ $radius->id }}" radius="{{ $radius->radius }}">
                                                            <img
                                                                src="{{ asset('/assets/images/template-images/writing-1.png') }}"
                                                                style="width:20px; height: 20px;"
                                                                data-toggle="tooltip"
                                                                data-placement="top" title="Edit">
                                                        </a>
                                                        <a class="delete"
                                                           search_radius_id="{{ $radius->id }}"
                                                        >
                                                            <img src="{{ asset('/assets/images/template-images/remove-1.png') }}"
                                                                 style="width:20px; height: 20px;" data-toggle="tooltip"
                                                                 data-placement="top" title="Delete">
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
    <div class="md-modal md-effect-1" id="modal-3">
        <div class="md-content">
            <h3 class="bg-c-blue heading"></h3>
            <div class="wrapper">
                <div class="cover-spin" style="display: none"></div>
                <form method="post" name="add_radius_form"  id="add_radius_form" action="{{ route('post:admin:update_search_radius') }}" >
                    {{csrf_field() }}
                    <p id="send_message" class="text-success font-weight-bold"></p>
                    <input type="hidden" class="form-control" name="radius_id" id="radius_id" placeholder="Radius id" value="">
                    <div class="form-group">
                        <label class="col-form-label">Radius:</label>
                        <input type="number" name="radius" class="form-control border-r-top-left-right" min="1" required id="radius" value="{{ old('radius') }}" placeholder="Enter new Radius">
                    </div>
                    <div class="form-group">
                        <p id="fail_message" class="text-danger"></p>
                    </div>
                    <button type="submit" class="btn btn-primary btn_model_send">Submit</button>
                    <button type="button" class="btn btn-login btn_model_close md-close">Close</button>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}"></script>
    <script src="{{ asset('assets/js/classie.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize validation
            $("#add_radius_form").validate({
                rules: {
                    radius: {
                        required: true,
                        number: true,
                        min: 1
                    }
                },
                messages: {
                    radius: {
                        required: "Please enter radius",
                        number: "Radius must be a number",
                        min: "Radius must be at least 1"
                    }
                },
                errorElement: "span",
                errorClass: "text-danger",
                // Show loader on valid submit
                submitHandler: function (form) {
                    $(".cover-spin").show();
                    $(".btn_model_send").prop("disabled", true);
                    form.submit();
                }
            });

            // Reset validation & messages when modal closes
            $(".md-close").on("click", function () {
                $("#add_radius_form")[0].reset();
                $("#add_radius_form").validate().resetForm();
                $("#radius").removeClass("is-invalid");
                $("#fail_message, #send_message").html("");
                $(".cover-spin").hide();
                $(".btn_model_send").prop("disabled", false);
            });

        });
        var table = $('#new-cons').DataTable();
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var id = $(this).attr('search_radius_id');
            var RemovetableRow = table.row($(this).parents('tr'));
            swal({
                    title: "Are you sure?",
                    text: "You will not be able to recover this Search Radius!",
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
                            url: '{{ route('get:ajax:admin:delete_search_radius') }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == 0) {
                                    swal("Warning", "You can not delete this Search Radius", "success");
                                }
                                if (result.success == true) {
                                    RemovetableRow.remove().draw();
                                    swal("Success", "Search Radius Delete Successfully", "success");
                                }
                                else {
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
        $(document).ready(function (){
            $(document).on("click",".add_search_radius",function (){
                var modal = document.querySelector('#modal-3');
                close = modal.querySelector('.md-close');
                classie.add(modal, 'md-show');
                $('.heading').html('Add Search Radius');
            });
            $(document).on("click",".edit_search_radius",function (){
                var  id=$(this).attr("search_radius_id");
                var radius=$(this).attr("radius");
                var modal = document.querySelector('#modal-3');
                close = modal.querySelector('.md-close');
                classie.add(modal, 'md-show');
                $('#radius_id').val(id);
                $('#radius').val(radius);
                $('.heading').html('Edit Search Radius');

            });
            $(document).on("click",".md-close",function (){
                var modal = document.querySelector('#modal-3');
                classie.remove(modal, 'md-show');
                $("#add_radius_form")[0].reset();
            });

        });
    </script>
@endsection

