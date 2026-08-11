@extends('admin.layout.super_admin')
@section('title', __('admin.pages.ride_details'))
@section('page-css')
    <style>
        #map {
            z-index: 0;
        }

        .set-map {
            height: 300px;
            margin-bottom: 30px;
        }

        .ride-status span {
            font-size: 14px;
            padding: 3px 6px;
        }
    </style>
    <!-- Modal css -->
    <style>
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
            top: 10%;
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
            margin: 100px auto;
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
            background: aliceblue;
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

        @-webkit-keyframes spin {
            from {-webkit-transform:rotate(0deg);}
            to {-webkit-transform:rotate(360deg);}
        }

        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
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
                            <div class="page-header-title">
                                <i class="feather icon-list bg-c-green"></i>
                                <div class="d-inline">
                                    <h5>{{ __('admin.pages.ride_details') }}</h5>
                                    <span>Full Ride Details</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            @if(in_array($ride_Details->status, [0,1,2,3,5,6,7,8]))
                                <button class="btn btn-danger waves-effect waves-light btn-right m-b-0 ride_cancel" rideid="{{$ride_Details->id}}" updatestatus="4">{{ __('admin.common.cancel') }}</button>
                            @endif
{{--                            @if((Illuminate\Support\Facades\Auth::guard("admin")->check()) && (Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 1 || Illuminate\Support\Facades\Auth::guard("admin")->user()->roles == 4))--}}
{{--                                @if((isset($general_settings)) && $general_settings != Null && isset($ride_Details))--}}
{{--                                    @if(($general_settings->max_driver_reassign > $ride_Details->is_driver_reassign) && ($ride_Details->driver_id != Null) && in_array($ride_Details->status,[1,2,3,5]))--}}
{{--                                        <a href="{{ route('get:admin:transport_re_assign_driver',[ "ride_id" => $ride_Details->id ]) }}" class="btn btn-dark waves-effect waves-light  btn-right m-b-0 m-r-10 ride_re_assign">Re-Assign</a>--}}
{{--                                    @endif--}}
{{--                                @endif--}}
{{--                            @endif--}}
                            @if(in_array($ride_Details->status, [1,2,3,5,6,7,8]))
                                <button class="btn btn-success waves-effect waves-light btn-right m-b-0 ride_completed" rideid="{{$ride_Details->id}}" updatestatus="9" style="margin-right: 10px;">{{ __('admin.status.completed') }}</button>
                            @endif
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
                                <h5>Ride route on map</h5>
                            </div>
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12">
                                        <div id="map" class="set-map"></div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="table-responsive ride-detail-table">
                                            <div class="ride-detail-table-header">
                                                <h5>Ride Detail</h5>
                                            </div>
                                            <table class="table">
                                                @if(isset($ride_Details))
                                                    <tr>
                                                        <th>Ride No.</th>
                                                        <td>: {{ $ride_Details->ride_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_name') }}</th>
                                                        <td>:
                                                            @if($ride_Details->is_hail == 1)
                                                                @if($ride_Details->other_user_name != Null) {{ ucwords(strtolower($ride_Details->other_user_name)) }} @else ---- @endif
                                                            @else
                                                                @if($ride_Details->user_name != Null)
                                                                    {{ ucwords(strtolower($ride_Details->user_name)) }} @else ---- @endif
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_contact_number') }}</th>
                                                        <td>:
                                                            @if($ride_Details->is_hail == 1)
                                                                @if((isset($ride_Details)) && $ride_Details->other_user_contact_number != Null) {{ App\Models\User::ContactNumber2Stars($ride_Details->other_user_contact_number) }} @else ---- @endif
                                                            @else
                                                                @if((isset($user_details)) && $user_details->contact_number != Null) {{ App\Models\User::ContactNumber2Stars($user_details->country_code."".$user_details->contact_number) }} @else ---- @endif
                                                           @endif
                                                        </td>
                                                    </tr>
                                                    @if($ride_Details->ride_for_other == 1)
                                                        <tr>
                                                            <th>{{ __('admin.columns.booked_for_customer_name') }}</th>
                                                            <td>: @if($ride_Details->other_user_name != Null) {{ ucwords(strtolower($ride_Details->other_user_name)) }} @else ---- @endif</td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ __('admin.columns.booked_for_customer_contact') }}</th>
                                                            <td>: @if((isset($ride_Details)) && $ride_Details->other_user_contact_number != Null) {{ App\Models\User::ContactNumber2Stars($ride_Details->other_user_contact_number) }} @else ---- @endif</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_email') }}</th>
                                                        <td>: @if((isset($user_details)) && $user_details->email != Null) {{ App\Models\User::Email2Stars($user_details->email) }} @else ---- @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_name') }}</th>
                                                        <td>: @if($ride_Details->driver_name != Null) {{ ucwords(strtolower($ride_Details->driver_name)) }} @else ---- @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_contact_number') }}</th>
                                                        <td>: @if((isset($driver_details)) && $driver_details->contact_number != Null) {{ App\Models\User::ContactNumber2Stars($driver_details->country_code."".$driver_details->contact_number) }} @else ---- @endif </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_email') }}</th>
                                                        <td>: @if((isset($driver_details)) && $driver_details->email != Null) {{ App\Models\User::Email2Stars($driver_details->email) }} @else ---- @endif
                                                        </td>
                                                    </tr>
{{--                                                    <tr>--}}
{{--                                                        <th>{{ __('admin.columns.ride_type') }}</th>--}}
{{--                                                        <td>: {{ isset($ride_Details) && $ride_Details->ride_type == 1 ? "Schedule Ride" : 'Book Now' }}</td>--}}
{{--                                                    </tr>--}}
                                                    <tr>
                                                        <th>{{ __('admin.columns.ride_booking_date') }}</th>
                                                        <td>: {{ isset($ride_Details) ? $ride_Details->created_at : '' }}</td>
                                                    </tr>
                                                    @if(isset($ride_Details) && $ride_Details->ride_type == 1)
                                                        <tr>
                                                            <th>{{ __('admin.columns.schedule_ride_date') }}</th>
                                                            <td>: {{ isset($ride_Details) ? $ride_Details->pickup_datetime : '' }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <th>{{ __('admin.columns.pickup_address') }}</th>
                                                        <td>: {{ $ride_Details->pickup_address }}</td>
                                                    </tr>
                                                    @for($i=1 ;$i<=3;$i++)
                                                        @if(isset($ride_Details['way_point_'.$i]) && $ride_Details['way_point_'.$i] !="" )
                                                            <tr>
                                                                <th>Way Point {{ $i }}</th>
                                                                <td>: {{ $ride_Details['way_point_'.$i] }}</td>
                                                            </tr>
                                                        @endif
                                                    @endfor
                                                    <tr>
                                                        <th>{{ __('admin.columns.destination_address') }}</th>
                                                        <td>: {{ $ride_Details->destination_address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.pages.vehicle_type') }}</th>
                                                        <td>: @if($ride_Details->vehicle_type_name != Null) {{ ucwords(strtolower($ride_Details->vehicle_type_name)) }} @else ---- @endif </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="table-responsive ride-detail-table">
                                            <div class="ride-detail-table-header"><h5>Fare Detail</h5></div>
                                            <table class="table" id="fare_detail_table">
                                                @if(isset($ride_Details))
                                                    <tr>
                                                        <th>{{ __('admin.columns.offered_price') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->offered_price }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.total_distance') }}</th>
                                                        <td>: {{ $ride_Details->total_distance." km" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.time_taken') }}</th>
                                                        <td>: {{ $ride_Details->eta > 0 ? $ride_Details->eta . " min" : 0 . " min" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.refer_discount') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->refer_discount }}</td>
                                                    </tr>
                                                    @if($ride_Details->toll_charge > 0)
                                                        <tr>
                                                            <th>{{ __('admin.columns.toll_charge') }}</th>
                                                            <td>: <span class="currency"></span><span id="toll_charge_label"> {{ $ride_Details->toll_charge }}</span></td>
                                                        </tr>
                                                    @endif
                                                    <tr  id="total_row">
                                                        <th>{{ __('admin.columns.total') }}</th>
                                                        <td id="total_pay_label">: <span class="currency"></span> {{ isset($grand_total)? $grand_total : 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.payment_type') }}</th>
                                                        <td>: {{ isset($ride_Details) ? ($ride_Details->payment_type == 1 ? "Cash" : ($ride_Details->payment_type == 2 ? "Online" : ($ride_Details->payment_type == 3 ? "Wallet" : ''))) : '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.payment_status') }}</th>
                                                        <td>: {{ ($ride_Details->payment_status == 1) ? 'Paid' : 'Not Paid' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.common.status') }}</th>
                                                        @if($ride_Details->status == 0)
                                                            @php $ride_status = "pending"; @endphp
                                                        @elseif($ride_Details->status == 1 || $ride_Details->status == 2 || $ride_Details->status == 3)
                                                            @php $ride_status = "approved"; @endphp
                                                        @elseif($ride_Details->status == 4)
                                                            @php $ride_status = "cancelled"; @endphp
                                                        @elseif($ride_Details->status == 5 || $ride_Details->status == 6 || $ride_Details->status == 7 || $ride_Details->status == 8)
                                                            @php $ride_status = "running"; @endphp
                                                        @elseif($ride_Details->status == 9)
                                                            @php $ride_status = "completed"; @endphp
                                                        @elseif($ride_Details->status == 10)
                                                            @php $ride_status = "failed"; @endphp
                                                        @endif
                                                        <td class="ride-status">: <span id="ride_status_change" class="{{ $ride_status }}"> {{ ucwords($ride_status) }}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <th>OTP</th>
                                                        <td class="otp-section">: {{ isset($ride_Details->otp) ? $ride_Details->otp : '---' }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                        @if(isset($courier_details) && $courier_details != Null)
                                            <div class="table-responsive ride-detail-table">
                                                <div class="ride-detail-table-header"><h5>Courier Detail</h5></div>
                                                <table class="table">
                                                    @if(isset($courier_details))
                                                        <tr>
                                                            <th>{{ __('admin.columns.recipient_name') }}</th>
                                                            <td>: {{ $courier_details->recipient_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ __('admin.columns.recipient_contact_number') }}</th>
                                                            <td>: {{ $courier_details->recipient_contact_number }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ __('admin.columns.item_description') }}</th>
                                                            <td>: {{ $courier_details->item_description }}</td>
                                                        </tr>
                                                        @if($courier_details->estimate_price > 0)
                                                        <tr>
                                                            <th>{{ __('admin.columns.estimate_price') }}</th>
                                                            <td>: <span class="currency"></span> {{ $courier_details->estimate_price }}</td>
                                                        </tr>
                                                        @endif
                                                    @endif
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>

        <!-- Toll Charge Modal Code -->
        <!--code for dynamic Toll charge module is_toll_module = 0 - off , 1 - driver will give the final charge , 2 - driver will give no of tolls & charge per toll is decided by admin-->
        <div class="md-modal md-effect-1" id="modal">
            <div class="md-content">
                <h3 class="bg-c-blue">Add Toll Charge</h3>
                <div class="wrapper">
                    <form method="get" id="apply_toll_charge">
                        <p id="send_message_2" class="text-success font-weight-bold"></p>
                        <input type="hidden" name="id" value="{{$ride_Details->id}}">
                        <input type="hidden" name="update_status" value="9">
                        <input type="hidden" id="no_of_toll" value="{{$ride_Details->no_of_toll}}">
                        @if($general_settings->is_toll_module == 1)
                            <div class="form-group">
                                <label class="col-form-label">{{ __('admin.forms.toll_charge_question') }}</label>
                                <input type="number" class="form-control" name="toll_charge" value="0" id="toll_charge" placeholder="0">
                            </div>
                        @elseif($general_settings->is_toll_module == 2)
                            <div class="form-group">
                                <label class="col-form-label">{{ __('admin.forms.no_of_tolls') }}</label>
                                <input type="number" class="form-control" name="no_of_toll" required min="0" id="toll_charge" placeholder="{{ __('admin.forms.enter_no_of_toll') }}">
                            </div>
                        @endif
                        <div class="form-group">
                            <p id="fail_message" class="text-danger"></p>
                        </div>
                        <button type="submit" class="btn btn-primary btn_model_send_2">{{ __('admin.common.yes') }}</button>
                        <button type="submit" id="no_button" class="btn btn-login btn_model_close_2 md-close-2">{{ __('admin.common.no') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    @if(Illuminate\Support\Facades\Auth::guard("admin")->check())
        <script>
            $(document).on('click', '.ride_cancel', function (e) {
                e.preventDefault();
                var id = $(this).attr('rideid');
                var update_status = $(this).attr('updatestatus');
                var txt, title, status, url;
                status = "Cancel";

                url = '{{ route("get:admin:transport_update_ride_status") }}';
                title = status + " Ride?";
                txt = "if press yes then " + status + " ride!";
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
                            swal({
                                    title: status + " Ride!",
                                    text: "if " + status + " ride then Write " + status + " reason:",
                                    type: "input",
                                    showCancelButton: true,
                                    closeOnConfirm: false,
                                    animation: "slide-from-top",
                                    inputPlaceholder: status + " Reason"
                                },
                                function (inputValue) {
                                    if (inputValue === false) return false;
                                    if (inputValue === "") {
                                        swal.showInputError("You need to write " + status + " ride!");
                                        return false
                                    }
                                    $.ajax({
                                        type: 'get',
                                        url: url,
                                        data: {id: id, update_status: update_status, reason: inputValue},
                                        success: function (result) {
                                            if (result.success == true) {
                                                $(".ride_cancel").css({"display": "none"});
                                                $(".ride_re_assign").css({"display": "none"});
                                                $(".ride_completed").css({"display": "none"});
                                                if ($("#ride_status_change").hasClass("pending")) {
                                                    $("#ride_status_change").removeClass("pending");
                                                }
                                                if ($("#ride_status_change").hasClass("approved")) {
                                                    $("#ride_status_change").removeClass("approved");
                                                }
                                                if ($("#ride_status_change").hasClass("running")) {
                                                    $("#ride_status_change").removeClass("running");
                                                }
                                                $("#ride_status_change").addClass("cancelled");
                                                $("#ride_status_change").empty();
                                                $("#ride_status_change").text("Cancelled");
                                                swal("success!", "You " + status + " ride reason is: " + inputValue, "success");
                                                location.reload();
                                            }else {
                                                swal("Warning", result.message, "warning");
                                                console.log(result);
                                            }
                                        }
                                    })
                                });
                        } else {
                            swal("Cancelled", "Ride Status not changed", "error");
                        }
                    });
            });

            $(document).on('click', '.ride_completed', function (e) {
                e.preventDefault();
                var id = $(this).attr('rideid');
                var update_status = $(this).attr('updatestatus');
                var status = "Completed";
                var url = '{{ route("get:admin:transport_update_ride_status") }}';

                swal({
                    title: status + " Ride?",
                    text: "If press yes then " + status + " ride!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {

                    if (!isConfirm) {
                        swal("Cancelled", "Ride Status Not Updated", "error");
                        return;
                    }

                    /* -------------------------------------------------
                     | Toll logic
                     -------------------------------------------------*/

                    @if($general_settings->is_toll_module > 0)

                    // check if toll charge input exists on page
                    var tollInput = $('input[name="toll_charge"], input[name="no_of_toll"]');

                    if (tollInput.length > 0) {

                        // show modal only if toll is applicable
                        $('.showSweetAlert').hide();
                        var modal = document.querySelector('#modal');
                        modal.classList.add('md-show');

                        // YES in modal → submit with toll
                        $('.btn_model_send_2').off('click').on('click', function (e) {
                            e.preventDefault();

                            var form_data = $("#apply_toll_charge").serialize();
                            submitRide(form_data);
                        });

                        // NO in modal → submit without toll
                        $('#no_button').off('click').on('click', function (e) {
                            e.preventDefault();

                            submitRide({ id: id, update_status: update_status });
                        });

                    } else {
                        // no toll inputs → submit directly
                        submitRide({ id: id, update_status: update_status });
                    }

                    @else
                    // toll module OFF → submit directly
                    submitRide({ id: id, update_status: update_status });
                    @endif

                });

                /* -------------------------------------------------
                 | Submit function
                 -------------------------------------------------*/
                function submitRide(data) {
                    var modal = document.querySelector('#modal');
                    if (modal && modal.classList.contains('md-show')) {
                        modal.classList.remove('md-show');
                    }
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        success: function (result) {

                            if (result.success === true) {

                                $(".ride_cancel, .ride_re_assign, .ride_completed").hide();

                                $("#ride_status_change")
                                    .removeClass("pending approved running cancelled failed")
                                    .addClass("completed")
                                    .text("Completed");
                                var tollCharge =
                                    $('input[name="toll_charge"]').val() ||
                                    result.toll_charge || 0;

                                if (parseFloat(tollCharge) > 0) {

                                    // If row already exists → update
                                    if ($('#toll_charge_label').length > 0) {
                                        $('#toll_charge_label').text(tollCharge);
                                    } else {
                                        // Insert Toll Charge row BEFORE Total row
                                        $('#total_row').before(`
                                            <tr id="toll_charge_row">
                                                <th>{{ __('admin.columns.toll_charge') }}</th>
                                                <td>: <span class="currency"></span>
                                                    <span id="toll_charge_label">${tollCharge}</span>
                                                </td>
                                            </tr>
                                        `);
                                    }
                                }
                                if (result.total_pay !== undefined) {
                                    $('#total_pay_label').text(result.total_pay);
                                }

                                swal("Success", "Ride status update successfully", "success");
                                // location.reload();
                            } else {
                                swal("Warning", result.message || "Something went wrong", "warning");
                            }
                        },
                        error: function () {
                            swal("Error", "Server error occurred", "error");
                        }
                    });
                }
            });
        </script>
    @endif
    @if(isset($ride_Details) && $ride_Details->destination_address != null && $ride_Details->pickup_address != null)
        <script>
            function initMap() {
                var lati = "{{ isset($general_settings) && $general_settings->map_lat ? $general_settings->map_lat : 22.3039 }}";
                var longi = "{{ isset($general_settings) && $general_settings->map_long ? $general_settings->map_long : 70.8022 }}";

                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 7,
                    center: {lat: parseFloat(lati), lng: parseFloat(longi)}
                });
                directionsDisplay.setMap(map);
                calculateAndDisplayRoute(map);
            }

            async function calculateAndDisplayRoute(map) {
                const origin = "{{ isset($ride_Details) ? $ride_Details->pickup_address : '' }}";
                const destination = "{{ isset($ride_Details) ? $ride_Details->destination_address : '' }}";

                const apiKey = "{{ isset($general_settings) && $general_settings->map_key ? $general_settings->map_key : '' }}";
                const url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Goog-Api-Key': apiKey,
                        'X-Goog-FieldMask': 'routes.polyline.encodedPolyline'
                    },
                    body: JSON.stringify({
                        origin: {
                            address: origin
                        },
                        destination: {
                            address: destination
                        },
                        travelMode: 'DRIVE'
                    })
                });

                if (!response.ok) {
                    alert('Directions request failed: ' + response.statusText);
                    return;
                }

                const data = await response.json();

                if (data.routes && data.routes[0] && data.routes[0].polyline) {
                    const encodedPolyline = data.routes[0].polyline.encodedPolyline;

                    // Decode and draw the polyline
                    const path = google.maps.geometry.encoding.decodePath(encodedPolyline);
                    const routeLine = new google.maps.Polyline({
                        path: path,
                        geodesic: true,
                        strokeColor: '#454545',
                        strokeOpacity: 1.0,
                        strokeWeight: 4
                    });

                    routeLine.setMap(map);
                    // Get bounds of the polyline and adjust map zoom and center
                    const bounds = new google.maps.LatLngBounds();
                    path.forEach(function (latLng) {
                        bounds.extend(latLng);
                    });

                    map.fitBounds(bounds);
                    //  Add pickup (origin) marker
                    new google.maps.Marker({
                        position: path[0], // First point
                        map: map,
                        label: "A",
                        title: "Pickup Location",
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#39AD48',
                            fillOpacity: 1,
                            strokeWeight: 1,
                            strokeColor: 'white'
                        },
                    });

                    //  Add drop (destination) marker
                    new google.maps.Marker({
                        position: path[path.length - 1], // Last point
                        map: map,
                        label: "B",
                        title: "Drop Location",
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#FF0000',
                            fillOpacity: 1,
                            strokeWeight: 1,
                            strokeColor: 'white'
                        },
                    });
                } else {
                    alert('No route found or polyline data is missing.');
                }
            }
        </script>

        <script src="https://maps.googleapis.com/maps/api/js?key={{ isset($general_settings) && $general_settings->map_key ? $general_settings->map_key : '' }}&libraries=geometry&callback=initMap" async defer></script>
    @endif
@endsection

