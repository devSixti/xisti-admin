@extends('admin.layout.super_admin')
@section('title')
    @if(isset($status) && $status == 1 ) {{ __('admin.pages.approved') }} @elseif(isset($status) && $status == 0) {{ __('admin.pages.unapproved') }} @elseif(isset($status) && $status == 2) {{ __('admin.pages.blocked') }} @elseif(isset($status) && $status == 3) {{ __('admin.pages.rejected') }} @endif Drivers List
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <!-- Data Table Excel Css -->
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/responsive/buttons.dataTables.min.css?v=0.1')}}">
    <style>
        /*datatable style*/
        table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child:before, table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child:before {
            background: #55d090;
        }

        .page-item.active .page-link {
            background: #55d090;
            border-color: #55d090;
        }

        .page-link {
            color: #55d090;
        }

        /*datatable td link*/
        .document a, .ride a, .ratings a {
            color: #55d090;
            font-weight: bold;
            font-size: 14px;
        }

        .document i, .ride i, .ratings i {
            font-size: 18px;
        }

        .ratings i {
            font-size: 16px;
        }

        .icon-list-demo i {
            height: auto;
            line-height: 10px;
            border: none;
            margin-right: 5px;
            color: #55d090;
        }

        /* Status style*/
        .toggle {
            display: inline-block;
        }

        .toggle input[type="checkbox"]:checked + .button-indecator:before {
            color: #55d090;
        }

        .toggle input[type="checkbox"] + .button-indecator:before {
            color: #55d090;
        }

        /* Vehicle type styles for the modal */
        .md-perspective,
        .md-perspective body {
            height: 100%;
            /* allow page scroll */
        }

        .md-perspective body {
            background: #222;
            -webkit-perspective: 600px;
            -moz-perspective: 600px;
            perspective: 600px;
        }

        .md-modal {
            position: fixed;
            top: 20%;
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

        /*.md-trigger:hover {*/
        /*    color: #64b0f2;*/
        /*    cursor: pointer;*/
        /*}*/

        .md-trigger img:hover {
            opacity: 0.7;
            cursor: pointer;
        }
        .cover-spin   {
            position:fixed;
            width:100%;
            left:0;right:0;top:0;bottom:0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index:9999;
            /*display:none;*/
        }
        .cover-spin::after {
            content:'';
            display:block;
            position:absolute;
            left:48%;
            top:40%;
            width:50px;
            height:50px;
            border-style:solid;
            border-color:black;
            border-top-color:transparent;
            border-width: 4px;
            border-radius:50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
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
        @-webkit-keyframes spin {
            from {-webkit-transform:rotate(0deg);}
            to {-webkit-transform:rotate(360deg);}
        }

        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
        }
        @if((isset($status) && $status==2) || (isset($status) && $status==3))
            .toggle input[type="checkbox"]:checked + .button-indecator:before {
            color: red;
        }
        @endif
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <div class="other-service-horizontal-nav">
            {{--@include('admin.include.transport-horizontal-navbar')--}}
        </div>

        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-list bg-c-green"></i>
                        <div class="d-inline">
                            <h5> @if(isset($status) && $status == 1 ) {{ __('admin.pages.approved') }} @elseif(isset($status) && $status == 0) {{ __('admin.pages.unapproved') }} @elseif(isset($status) && $status == 2) {{ __('admin.pages.blocked') }} @elseif(isset($status) && $status == 3) {{ __('admin.pages.rejected') }} @endif
                                Drivers List</h5>
                            <span>{{ __('admin.pages.all_drivers_list') }}</span>
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
                                <h5>{{ __('admin.pages.all_drivers_list') }}</h5>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="drivers" class="table table-striped table-bordered nowrap" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th id="no">{{ __('admin.common.no') }}</th>
                                            <th>{{ __('admin.pages.vehicle_type') }}</th>
                                            <th>{{ __('admin.columns.driver_name') }}</th>
                                            <th>{{ __('admin.columns.email') }}</th>
                                            <th>{{ __('admin.forms.contact_no_label') }}</th>
                                            <th>{{ __('admin.columns.rating') }}</th>
                                            @if((isset($status) && $status == 1) || (isset($status) && $status == 2))
                                            <th>{{ __('admin.columns.trip') }}</th>
                                                @php
                                                $wallet_payment = 0;
                                                $general_settings = request()->get("general_settings");
                                                if ($general_settings != Null){
                                                    $wallet_payment = $general_settings->wallet_payment;
                                                }
                                                @endphp
                                                @if($wallet_payment == 1)
                                                <th>{{ __('admin.columns.wallet_balance') }}</th>
                                                @endif
                                            @endif
                                            <th>{{ __('admin.columns.documents') }}</th>
                                            @if(isset($status)&& in_array($status, [0,2,3]))
                                                <th>Sign-Up Time</th>
                                            @endif
                                            <th>{{ __('admin.columns.app_version') }}</th>
                                            <th>{{ __('admin.common.actions') }}</th>
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

    <div class="md-modal md-effect-1" id="modal-1">
        <div class="md-content">
            <h3 class="bg-c-green" id="driver_name_type">Nixon</h3>
            <div>
                <div><img id="vehicle_icon" src=""></div>
                <ul>
                    <li><strong>{{ __('admin.forms.vehicle_type_colon') }} : </strong> <span id="vehicle_name"></span></li>
                    <li><strong>{{ __('admin.forms.model_name') }} : </strong> <span id="model_name"></span></li>
                    <li><strong>{{ __('admin.forms.license_plate') }} : </strong> <span id="license_name"></span></li>
                    <li><strong>{{ __('admin.forms.vehicle_year') }} : </strong> <span id="vehicle_year"></span></li>
                    <li><strong>{{ __('admin.forms.vehicle_color') }} : </strong> <span id="vehicle_color"></span></li>
                </ul>
                <a href="" id="driver_vehicle_details">
                    <button type="button" class="btn btn-success waves-effect">{{ __('admin.common.edit') }}</button>
                </a>
                <button type="button" class="btn btn-success waves-effect md-close">{{ __('admin.forms.close') }}</button>
            </div>
        </div>
    </div>

    <div class="md-modal md-effect-1" id="modal-2">
        <div class="md-content">
            <h3 class="bg-c-green" id="driver_name">Nixon</h3>
            <div>
                <ul>
                    <li><strong>{{ __('admin.forms.total_request') }} : </strong> <span id="total_request"></span></li>
                    <li><strong>{{ __('admin.forms.total_completed') }} : </strong> <span id="total_completed"></span></li>
                    <li><strong>{{ __('admin.forms.total_rejected') }} : </strong> <span id="total_rejected"></span></li>
                </ul>
                <button type="button" class="btn btn-success waves-effect md-close">{{ __('admin.forms.close') }}</button>
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-1" id="modal-3">
        <div class="md-content">
            <h3 class="bg-c-blue">{{ __('admin.nav.change_password') }}</h3>
            <div class="wrapper">
                <div class="cover-spin" style="display: none"></div>
                <form method="get" id="change_password_form">
                    <p id="send_message" class="text-success font-weight-bold"></p>
                    <input type="hidden" class="form-control" name="provider_id" id="provider_id" placeholder="{{ __('admin.forms.provider_id') }}" value="">
                    <div class="form-group">
                        <label class="col-form-label">{{ __('admin.forms.password') }}:</label>
                        <input type="password" name="password" class="form-control border-r-top-left-right" required id="password" value="{{ old('password') }}" placeholder="{{ __('admin.forms.enter_new_password') }}">
                        <i class="far fa-eye" id="togglePassword" style="float: right; margin-top: -25px; margin-right: 10px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('admin.forms.confirm_password') }}:</label>
                        <input type="password" name="confirm_password" class="form-control border-r-top-left-right" required id="confirm_password" value="{{ old('forgot_confirm_password') }}" placeholder="{{ __('admin.forms.confirm_password') }}">
                        <i class="far fa-eye" id="toggleConfirmPassword" style="float: right; margin-top: -25px; margin-right: 10px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group">
                        <p id="fail_message" class="text-danger"></p>
                    </div>
                    <button type="submit" class="btn btn-primary btn_model_send">{{ __('admin.common.submit') }}</button>
                    <button type="button" class="btn btn-login btn_model_close md-close">{{ __('admin.forms.close') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="md-modal md-effect-1" id="modal-4">
        <div class="md-content">
            <h3 class="bg-c-blue">{{ __('admin.columns.wallet') }}</h3>
            <div class="wrapper">
                <div class="cover-spin" style="display: none"></div>
                <form method="get" id="wallet_transaction_form">
                    <p id="send_message_2" class="text-success font-weight-bold"></p>
                    <input type="hidden" class="form-control" name="provider_id" id="wallet_provider_id" placeholder="{{ __('admin.forms.provider_id') }}" value="">
                    <div class="form-group">
                        <label class="col-form-label">{{ __('admin.forms.wallet_amount') }}:</label>
                        <input type="number" min="1" name="wallet_amount" class="form-control border-r-top-left-right" required id="wallet_amount" value="{{ old('password') }}" placeholder="{{ __('admin.forms.enter_wallet_amount') }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">{{ __('admin.forms.choose_wallet_option') }}:</label>
                        <select name="choose_option" class="form-control border-r-top-left-right" required>
                            <option value="1">{{ __('admin.forms.add_money') }}</option>
                            <option value="2">{{ __('admin.forms.deduct_money') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p id="fail_message_2" class="text-danger"></p>
                    </div>
                    <button type="submit" class="btn btn-primary btn_model_send_2">{{ __('admin.common.submit') }}</button>
                    <button type="button" class="btn btn-login btn_model_close_2 md-close-2">{{ __('admin.forms.close') }}</button>

                </form>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>
@endsection
@section('page-js')
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}" type="text/javascript"></script>
    <!-- CDN for the Excel file -->
    <script src="{{asset('assets/js/responsive/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/jszip.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/buttons.html5.min.js')}}"></script>
    <script src="{{asset('assets/js/responsive/buttons.print.min.js')}}"></script>
    <script src="{{asset('assets/js/datatablecommonfunction.js')}}"></script>
    <script>

        function init() {
            var overlay = document.querySelector('.md-overlay');
            [].slice.call(document.querySelectorAll('.md-trigger-vehicle')).forEach(function (el, i) {
                var modal = document.querySelector('#' + el.getAttribute('data-modal')), close = modal.querySelector('.md-close');
                function removeModal(hasPerspective) {
                    classie.remove(modal, 'md-show');
                    if (hasPerspective) {
                        classie.remove(document.documentElement, 'md-perspective');
                    }
                }
                function removeModalHandler() {
                    removeModal(classie.has(el, 'md-setperspective'));
                }
                el.addEventListener('click', function (ev) {
                    var model_type = $(this).attr('model_type');
                    if (model_type == 0) {
                        $('#model_type_driver').css("display", "none");
                        $('#model_type_vehicle').css("display", "block");
                        var vehicle_id = $(this).attr('vehicle_id');
                        var img_path = $('#vehicle_type_icon_' + vehicle_id).attr('src');
                        if (img_path == '') {
                            $("#vehicle_icon").attr("src");
                        } else {
                            $('#vehicle_icon').attr('src', img_path);
                        }
                        var vehicle_type_name = $(this).attr('vehicle_type_name');
                        var vehicle_company = $(this).attr('vehicle_company');
                        var plat_no = $(this).attr('plat_no');
                        var model_year = $(this).attr('model_year');
                        var color = $(this).attr('color');
                        var url = $(this).attr('url');
                        var driver_name = $(this).attr('driver_name');
                        $('#driver_name_type').text(driver_name);
                        $('#vehicle_name').text(vehicle_type_name);
                        $('#model_name').text(vehicle_company);
                        $('#license_name').text(plat_no);
                        $('#vehicle_year').text(model_year);
                        $('#vehicle_color').text(color);
                        $('#driver_vehicle_details').attr('href', url);
                        @if(isset($service_category) && $service_category->id == 2)
                        var get_vehicle_handicap = $(this).attr('handicap');
                        console.log(get_vehicle_handicap);
                        $('#vehicle_handicap').text(get_vehicle_handicap);
                        var get_vehicle_child_seat = $(this).attr('child_seat');
                        $('#vehicle_child_seat').text(get_vehicle_child_seat);
                        @endif
                    }
                    if (model_type == 1) {
                        $('#model_type_vehicle').css("display", "none");
                        $('#model_type_driver').css("display", "block");
                        var total_request = $(this).attr('total_request');
                        var total_completed = $(this).attr('total_completed');
                        var total_cancelled = $(this).attr('total_cancelled');
                        var driver_name = $(this).attr('driver_name');
                        $('#driver_name').text(driver_name);
                        $('#total_request').text(total_request);
                        $('#total_completed').text(total_completed);
                        $('#total_rejected').text(total_cancelled);
                    }
                    classie.add(modal, 'md-show');
                    overlay.removeEventListener('click', removeModalHandler);
                    overlay.addEventListener('click', removeModalHandler);
                    if (classie.has(el, 'md-setperspective')) {
                        setTimeout(function () {
                            classie.add(document.documentElement, 'md-perspective');
                        }, 25);
                    }
                });
                close.addEventListener('click', function (ev) {
                    ev.stopPropagation();
                    removeModalHandler();
                });
            });
        }
    </script>

    {{--Model Script type detials--}}
    <script src="{{ asset('assets/js/classie.js')}}" type="text/javascript"></script>
    <script rel="stylesheet" src="{{ asset('assets/js/validation/jquery.validate.js')}}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#drivers').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                responsive: true,
                ajax: {
                    url: "{{route('get:admin:transport_service_provider_list_new')}}",
                    data: {
                        status : "{{ $status }}"
                    }
                },
                columns: [
                    {data: 'no', 'sortable': false},
                    {data: 'vehicle_type','sortable': false},
                    {data: 'driver_name'},
                    {data: 'email'},
                    {data: 'contact_number'},
                    {data: 'rating'},
                    @if( (isset($status) && $status == 1) ||  (isset($status) && $status == 2))
                        {data: 'rides', 'sortable': false},
                        @if($wallet_payment == 1)
                            {data: 'driver_wallet_balance_html', 'sortable': false},
                        @endif
                    @endif
                    {data: 'documents', 'sortable': false},
                    @if(isset($status)&& in_array($status, [0,2,3]))
                        {data: 'sign_up_time'},
                    @endif
                    {data: 'provider_app_version' ,'sortable': false},
                    {data: 'actions', 'sortable': false},
                ],
                drawCallback: function () {
                    init();
                },
                dom: '<"top"lBf>rt<"bottom"pi><"clear">',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        columns: ':not(.notexport)',
                        modifier: {
                            page: 'all',
                        },
                    },
                    text: window.adminExcelButtonText,
                    "action": window.newexportaction,
                }],
            });
        });

        $(document).ready(function (){
            $(document).on("click",".change-password",function (){
                var overlay = document.querySelector('.md-overlay');
                var data_modal = $(this).attr('data-modal');
                var providerid = $(this).attr('providerid');
                $("#provider_id").val(providerid);
                var modal = document.querySelector('#modal-3');
                close = modal.querySelector('.md-close');
                classie.add(modal, 'md-show');
            });
            $(document).on("click",".md-close",function (){
                var modal = document.querySelector('#modal-3');
                classie.remove(modal, 'md-show');
                $("#change_password_form")[0].reset();
                $("#send_message").text("");
                $("#send_message").hide();
                $('#fail_message').text("");
                $("#fail_message").show();
            });
        });

        $(document).on('click', '.block', function (e) {
              e.preventDefault();
             let select_element = $(this);
            var table = $('#drivers').DataTable();
            console.log(select_element);
            var id = $(this).attr('provider_service_id');
            var service_cat_id = $(this).attr('service_cat_id');
            {{--var service_cat_id = {{ isset($service_category) ? $service_category->id : 0 }}--}}
            swal({
                    title: "Block Driver?",
                    text: "if press yes then driver will be blocked!",
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
                            url: '{{ route("get:admin:transport_update_provider_status") }}',
                            data: {id: id, request_for: 2, service_cat_id: service_cat_id},
                            success: function (result) {
                                if (result.success == true) {
                                    var new_id = "#hide_" + id;
                                    swal("Success", "driver block successfully", "success");
                                    $(new_id).hide();
                                    $('.block_element_'+id).closest('.parent').hide();
                                    $('.block_element_'+id).closest('.child').hide();
                                    table.ajax.reload();
                                } else {
                                    swal("Warning", result.message, "warning");
                                    //swal({ title: "Warning", text: result.message, type: "warning" }, function() {

                                    //});
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "driver status not change", "error");
                        location.reload();
                    }
                });
        });
        $(document).on('click', '.unblock', function (e) {
            var table = $('#drivers').DataTable();
            var id = $(this).attr('provider_service_id');
            var service_cat_id = $(this).attr('service_cat_id');

            swal({
                title: "Unblock Driver?",
                text: "if press yes then driver will be unblocked!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: 'get',
                        async: false,
                        url: '{{ route("get:admin:transport_update_provider_status") }}',
                        data: {id: id, request_for: 1, service_cat_id: service_cat_id},
                        success: function (result) {
                            if (result.success == true) {
                                var new_id = "#hide_" + id;
                                swal("Success", "driver unblock successfully", "success");
                                $(new_id).hide();
                                $('.unblock_element_'+id).closest('.parent').hide();
                                $('.unblock_element_'+id).closest('.child').hide();
                                // Reload the DataTable
                            } else {
                                swal("Warning", result.message, "warning");
                                console.log(result);
                            }
                        }
                    })
                } else {
                    swal("Cancelled", "Driver status not change", "error");
                    table.ajax.reload();
                }
            });
        });
        $(document).on('click', '.approve', function (e) {
            e.preventDefault();
            var id = $(this).attr('provider_service_id');
            var service_cat_id = $(this).attr('service_cat_id');
            swal({
                    title: "Approve Driver?",
                    text: "if press yes then driver will be approved!",
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
                            url: '{{ route("get:admin:transport_update_provider_status") }}',
                            data: {id: id, request_for: 1, service_cat_id: service_cat_id},
                            success: function (result) {
                                if (result.success == true) {
                                    var new_id = "#hide_" + id;
                                    swal("Success", "driver approve successfully", "success");
                                    $(new_id).hide();
                                    $('.approve_element_'+id).closest('.parent').hide();
                                    $('.approve_element_'+id).closest('.child').hide();
                                    // reload datable
                                    $('#drivers').DataTable().ajax.reload(null,false);
                                } else {
                                    swal("Warning", result.message, "warning");
                                    console.log(result);
                                }
                            }
                        })
                    } else {
                        swal("Cancelled", "driver status not change", "error");
                    }
                });
        });
        $(document).on('click', '.reject', function (e) {
            // e.preventDefault();
            var id = $(this).attr('provider_service_id');
            var service_cat_id = $(this).attr('service_cat_id');
            swal({
                    title: "Reject Driver?",
                    text: "if press yes then driver will be rejected!",
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
                        swal({
                                title: "Reject Driver!",
                                text: "if reject driver then Write reject reason:",
                                type: "input",
                                showCancelButton: true,
                                closeOnConfirm: false,
                                animation: "slide-from-top",
                                inputPlaceholder: "Reject Reason"
                            },
                            function (inputValue) {
                                if (inputValue === false) return false;
                                if (inputValue === "") {
                                    swal.showInputError("You need to write reject driver reason!");
                                    return false
                                }
                                $.ajax({
                                    type: 'get',
                                    url: '{{ route("get:admin:transport_update_provider_status") }}',
                                    data: {
                                        id: id,
                                        request_for: 3,
                                        service_cat_id: service_cat_id,
                                        rejected_reason: inputValue
                                    },
                                    success: function (result) {
                                        if (result.success == true) {
                                            var new_id = "#hide_" + id;
                                            swal("Success", "driver reject successfully", "success");
                                            $(new_id).hide();
                                            $('.reject_element_'+id).closest('.parent').hide();
                                            $('.reject_element_'+id).closest('.child').hide();
                                        } else {
                                            swal("Warning", result.message, "warning");
                                            console.log(result);
                                        }
                                    }
                                })
                            });
                    } else {
                        swal("Cancelled", "driver status not change", "error");
                    }
                });
        });
        $(document).on('click', '.delete-driver', function (e) {
            e.preventDefault();
            var table = $('#drivers').DataTable();
            var id = $(this).attr('providerid');
            swal({
                    title: "{{ __('admin.common.delete') }}?",
                    text: "You will not be able to recover this driver!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "{{ __('admin.common.yes') }}, {{ __('admin.common.delete') }}!",
                    cancelButtonText: "{{ __('admin.common.no') }}, {{ __('admin.common.cancel') }}!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'get',
                            url: '{{ route('get:admin:delete_transport_provider') }}',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    swal("Success", "driver remove successfully", "success");
                                    table.ajax.reload();
                                } else {
                                    swal("Warning", result.message || "Unable to delete driver", "warning");
                                }
                            },
                            error: function (xhr) {
                                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                    ? xhr.responseJSON.message
                                    : "Request failed";
                                swal("Warning", msg, "warning");
                            }
                        });
                    } else {
                        swal("Cancelled", "driver not removed", "error");
                    }
                });
        });
    </script>
    <script rel="stylesheet" src="{{ asset('assets/js/validation/jquery.validate.js')}}"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password = document.querySelector('#password');
        const confirm_password = document.querySelector('#confirm_password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
            confirm_password.setAttribute('type', type);

            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>

    <script src="{{ asset('assets/js/classie.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function (){

            $("#wallet_transaction_form").validate({
                rules: {
                    wallet_amount: {
                        required : true,
                        number: true,
                        min : 1
                    },
                    choose_option: {
                        required : true,
                    },
                },
                messages: {
                    wallet_amount: {
                        required :"{{ __('admin.forms.wallet_amount_required') }}",
                        number: "Please enter valid amount field is required",
                    },
                    choose_option: {
                        required :"{{ __('admin.forms.choose_option_required') }}",
                    },
                },
                submitHandler: function(form) {
                    var form_data = $("#wallet_transaction_form").serialize();
                    $.ajax({
                        type: 'get',
                        url: '{{ route('get:admin:update_provider_wallet_transaction') }}',
                        data: form_data,
                        async:false,
                        cache:false,
                        success: function (result) {
                            $(".cover-spin").css('display',"block");
                            setTimeout(function (){
                                if (result.success == true) {
                                    $('#fail_message_2').text("");
                                    $("#fail_message_2").hide();

                                    $("#send_message_2").text("");
                                    $("#send_message_2").show();
                                    $('#send_message_2').text(result.message);

                                    $('#wallet_amount').val("");
                                    $('#choose_option').val("");

                                    $(".cover-spin").css('display',"none");
                                    var modal = document.querySelector('#modal-4');
                                    classie.remove(modal, 'md-show');
                                    $('#change_wallet_'+result.user_id).text(result.last_amount);
                                    // location.reload();
                                } else {
                                    $("#send_message_2").text("");
                                    $("#send_message_2").hide();

                                    $('#fail_message_2').text("");
                                    $("#fail_message_2").show();
                                    $('#fail_message_2').text(result.message);
                                    $(".cover-spin").css('display',"none");
                                }
                            },900);
                        }
                    });
                }
            });

            $(document).on("click",".md-trigger-2",function (){
                var overlay = document.querySelector('.md-overlay');
                var data_modal = $(this).attr('data-modal');
                var providerid = $(this).attr('providerid');
                $("#wallet_provider_id").val(providerid);
                var modal = document.querySelector('#modal-4');
                close = modal.querySelector('.md-close-2');
                classie.add(modal, 'md-show');
                $("#wallet_amount").val();
                $("#send_message_2").hide();
                $("#wallet_transaction_form").validate().resetForm();
            });
            $(document).on("click",".md-close-2",function (){
                var modal = document.querySelector('#modal-4');
                classie.remove(modal, 'md-show');
                $("#wallet_amount").val('');
                $("#send_message_2").hide();
                $("label.error").hide();
                $(".error").removeClass("error");
                $("#wallet_transaction_form").validate().resetForm();
            })
        });
    </script>


@endsection

