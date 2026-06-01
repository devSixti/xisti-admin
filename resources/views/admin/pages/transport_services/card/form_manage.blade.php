@extends('admin.layout.driver_service')
@section('title')
    Card Lists
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
{{--    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">--}}
    <link href="{{ asset('/assets/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet" media="screen">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>Card Lists</h5>
                            <span>Card Lists</span>
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
                            <div class="form-group col-sm-12">
                                <form id="add_card" method="post"
                                      action="{{ route('post:driver-admin:driver_update_card') }}"
                                      enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Card Lists</h5>
                                            {{--<a href="{{ route('get:admin:user_list') }}"--}}
                                            {{--class="btn btn-primary m-b-0 btn-right render_link"> Back</a>--}}
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Card Holder Name:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control"
                                                                   name="card_holder_name" id="card_holder_name"
                                                                   placeholder="Enter Card Holder Name"
                                                                   required autocomplete="off" value="{{ old('card_holder_name') }}">
                                                            <span
                                                                class="error">{{ $errors->first('card_holder_name') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Expiry Date:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="expiry_date"
                                                                   id="datepicker1" placeholder="Enter Expiry Date"
                                                                   autocomplete="off" required value="{{ old('expire_date') }}">
                                                            <span
                                                                class="error">{{ $errors->first('expire_date') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Card Number:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="text" class="form-control" name="card_number"
                                                                   id="card_number" placeholder="Enter Your Card Number"
                                                                   autocomplete="off" required value="{{ old('card_number') }}"
                                                                   minlength="14" maxlength="16">
                                                            <span
                                                                class="error">{{ $errors->first('card_number') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">CVV:<sup
                                                                class="error">*</sup></label>
                                                        <div class="col-sm-8">
                                                            <input type="password" class="form-control" name="cvv"
                                                                   id="cvv" placeholder="Enter cvv" maxlength="3" minlength="3"
                                                                   autocomplete="off" required value="{{ old('cvv') }}">
                                                            <span
                                                                class="error">{{ $errors->first('cvv') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <center>
                                                <button type="submit" class="btn btn-primary m-b-0">Add</button>
                                            </center>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Card List</h5>
                                    </div>
                                    <div class="card-block">

                                        <div class="dt-responsive table-responsive">
                                            <table id="new-cons" class="table table-striped table-bordered nowrap"
                                                   style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Card Number</th>
                                                    <th>Holder Name</th>
                                                    <th>Expiry</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(isset($card_lists))
                                                    @foreach($card_lists as $key => $single_card)
                                                        <tr id="remove_card_{{ $single_card->id }}">
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>{{ $single_card->card_number }}</td>
                                                            <td>{{ $single_card->holder_name }}</td>
                                                            <td>{{ $single_card->month .'/'. $single_card->year }}</td>
                                                            <td>
                                                                <a class="delete" card_id="{{$single_card->id}}">
                                                                    <img src="{{ asset('/assets/images/template-images/remove-1.png') }}"
                                                                         style="width:20px; height: 20px;"
                                                                         data-toggle="tooltip"
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
    <script src="{{ asset('assets/js/validation/jquery.validate.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#add_card").validate({
                rules: {
                    card_holder_name: {
                        required: true,
                    },
                    expiry_date: {
                        required : true,
                    },
                    card_number: {
                        required : true,
                        number : true,
                    },
                    cvv: {
                        required : true,
                        number : true,
                    },
                },
                messages: {
                    card_holder_name: {
                        required :"Please enter card holder name",
                    },
                    expiry_date: {
                        required :"Please enter expiry date",
                    },
                    card_number: {
                        required :"please enter card number",
                        number : "please enter only number"
                    },
                    cvv: {
                        required :"please enter cvv",
                        number : "please enter only number"
                    }

                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
    <script type="text/javascript" src="{{asset('assets/js/bootstrap-datetimepicker.js')}}" charset="UTF-8"></script>
    <script type="text/javascript">
        // $('#datepicker1').datetimepicker({
        //     format: "yyyy-mm",
        //     autoclose: true,
        //     minView: 'month',
        //     pickTime: false,
        //     minDate:new Date(),
        //     fontAwesome: true,
        //     startDate: new Date(),
        //     icons: {
        //
        //         leftArrow: "fa fa-caret-left",
        //         rightArrow: "fa fa-caret-right",
        //     }
        // });
        $('#datepicker1').datetimepicker({
            format: "yyyy-mm",
            autoclose: true,
            minView: 3,
            minDate : new Date(),
            fontAwesome : true,
            startDate: new Date(),
            startView: "decade",
            maxView: 5,
            viewSelect: 'decade',
            icon : {
            }
        });
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var card_id = $(this).attr('card_id');
            swal({
                    title: "Are you sure to delete this card details?",
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
                            url: '{{ route('get:driver-admin:driver_delete_card') }}',
                            data: {card_id: card_id},
                            success: function (result) {
                                if (result.success == true) {
                                    swal("success", result.errorMsg, "success");
                                    // location.reload();
                                    $('#remove_card_'+card_id).hide();
                                }else{
                                    swal("Cancelled", result.errorMsg, "error");
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "Your card details is safe :)", "error");
                    }
                });
        });
    </script>
@endsection

