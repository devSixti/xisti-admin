@extends('admin.layout.driver_service')
@section('title')
    @if(isset($slug) && $slug == "courier-service")
        Delivery Details
    @else
        Ride Details
    @endif
@endsection
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
                                    <span> Ride Details</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            @if(isset($ride_Details))
                                @if(in_array($ride_Details->status, [1,2,3]))
                                    <button class="btn btn-danger waves-effect waves-light btn-right m-b-0 ride_cancel"
                                            rideid="{{$ride_Details->id}}" updatestatus="4">Cancel
                                    </button>
                                @endif
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
                                                <h5>@if(isset($slug) && $slug == "courier-service")
                                                        Delivery
                                                    @else Ride @endif Detail</h5>
                                            </div>
                                            <table class="table">
                                                @if(isset($ride_Details))
                                                    <tr>
                                                        <th>Ride No.</th>
                                                        <td>: {{ $ride_Details->ride_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_name') }}</th>
                                                        <td>
                                                            :
                                                            @if($ride_Details->user_name != Null)
                                                                {{ ucwords(strtolower($ride_Details->user_name)) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_contact_number') }}</th>
                                                        <td>
                                                            :
                                                            @if((isset($user_details)) && $user_details->contact_number != Null)
                                                                {{ App\Models\User::ContactNumber2Stars($user_details->country_code."".$user_details->contact_number) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.customer_email') }}</th>
                                                        <td>
                                                            :
                                                            @if((isset($user_details)) && $user_details->email != Null)
                                                                {{ App\Models\User::Email2Stars($user_details->email) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_name') }}</th>
                                                        <td>:
                                                            @if($ride_Details->driver_name != Null)
                                                                {{ ucwords(strtolower($ride_Details->driver_name)) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_contact_number') }}</th>
                                                        <td>:
                                                            @if((isset($driver_details)) && $driver_details->contact_number != Null)
                                                                {{ App\Models\User::ContactNumber2Stars($driver_details->country_code."".$driver_details->contact_number) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.driver_email') }}</th>
                                                        <td>:
                                                            @if((isset($driver_details)) && $driver_details->email != Null)
                                                                {{ App\Models\User::Email2Stars($driver_details->email) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    {{--<tr>
                                                        <th>{{ __('admin.columns.ride_time') }}</th>
                                                        <td>
                                                            : {{ Carbon\Carbon::parse($ride_Details->pickup_datetime)->format('d F Y h:i:s') }} </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.ride_end_time') }}</th>
                                                        <td>
                                                            : {{ Carbon\Carbon::parse($ride_Details->destination_datetime)->format('d F Y h:i:s') }}</td>
                                                    </tr>--}}
                                                    <tr>
                                                        <th>{{ __('admin.columns.ride_type') }}</th>
                                                        <td>: {{ isset($ride_Details) && $ride_Details->ride_type == 1 ? "Schedule Ride" : 'Book Now' }}</td>
                                                    </tr>
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
                                                    <tr>
                                                        <th>{{ __('admin.columns.destination_address') }}</th>
                                                        {{--<td>: Sydney Fish Market, 25 Bank St, Pyrmont NSW 2009, Australia</td>--}}
                                                        <td>: {{ $ride_Details->destination_address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.pages.vehicle_type') }}</th>
                                                        <td>
                                                            : @if($ride_Details->vehicle_type_name != Null)
                                                                {{ ucwords(strtolower($ride_Details->vehicle_type_name)) }}
                                                            @else
                                                                ----
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="table-responsive ride-detail-table">
                                            <div class="ride-detail-table-header">
                                                <h5>Fare Detail</h5>
                                            </div>
                                            <table class="table">
                                                @if(isset($ride_Details))
                                                    <tr>
                                                        <th>{{ __('admin.columns.base_fare') }}</th>
                                                        <td class="">
                                                            : <span class="currency"></span> {{ $ride_Details->base_fare }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.total_distance') }}</th>
                                                        <td>: {{ $ride_Details->total_distance }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.cost_per_km_label') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->vehicle_cost_for_km }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.distance_fare') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->total_distance_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.time_taken') }}</th>
                                                        <td>: {{ $ride_Details->eta }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Time Fare (<span class="currency"></span> {{ $ride_Details->vehicle_cost_for_min }} / 1 Min)</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->time_fare_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.discount') }}</th>
                                                        <td>: <span class="currency"></span> {{ isset($promo_code_discount)? $promo_code_discount : 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.surcharge_amount') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->surcharge_price }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.refer_discount') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->refer_discount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.subtotal') }}</th>
                                                        <td>: <span class="currency"></span> {{ isset($ride_Details->sub_total)? $ride_Details->sub_total : 0 }} </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.min_adjustment_amount') }}</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->adjustment_amount }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tax</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->tax }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tip</th>
                                                        <td>: <span class="currency"></span> {{ $ride_Details->tip }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.total') }}</th>
                                                        <td class="">
                                                            : <span class="currency"></span> {{ isset($grand_total)? $grand_total : 0 }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.payment_type') }}</th>
                                                        <td>
                                                            : {{ isset($ride_Details) ? ($ride_Details->payment_type == 1 ? "Cash" : ($ride_Details->payment_type == 2 ? "Card" : ($ride_Details->payment_type == 3 ? "Wallet" : ''))) : '' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.columns.payment_status') }}</th>
                                                        <td>
                                                            : {{ ($ride_Details->payment_status == 1) ? 'Paid' : 'Not Paid' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('admin.common.status') }}</th>
                                                        @if($ride_Details->status == 0)
                                                            @php $ride_status = "pending"; @endphp
                                                        @elseif($ride_Details->status == 1 || $ride_Details->status == 2 || $ride_Details->status == 3)
                                                            @php $ride_status = "approved"; @endphp
                                                            {{--@elseif($ride_list->status == 3)--}}
                                                            {{--@php $ride_status = "arrived"; @endphp--}}
                                                        @elseif($ride_Details->status == 4)
                                                            @php $ride_status = "cancelled"; @endphp
                                                        @elseif($ride_Details->status == 5 || $ride_Details->status == 6 || $ride_Details->status == 7 || $ride_Details->status == 8)
                                                            @php $ride_status = "running"; @endphp
                                                            {{--@elseif($ride_list->status == 6)--}}
                                                            {{--@php $ride_status = "drop"; @endphp--}}
                                                            {{--@elseif($ride_list->status == 7)--}}
                                                            {{--@php $ride_status = "payment"; @endphp--}}
                                                            {{--@elseif($ride_list->status == 8)--}}
                                                            {{--@php $ride_status = "rating"; @endphp--}}
                                                        @elseif($ride_Details->status == 9)
                                                            @php $ride_status = "completed"; @endphp
                                                        @elseif($ride_Details->status == 10)
                                                            @php $ride_status = "failed"; @endphp
                                                        @endif
                                                        <td class="ride-status">:
                                                            <span id="ride_status_change"
                                                                  class="{{ $ride_status }}"> {{ ucwords($ride_status) }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
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
                                            }else {
                                                swal("Warning", result.message, "warning");
                                                console.log(result);
                                            }
                                        }
                                    })
                                });
                        } else {
                            swal("Cancelled", "Cancel Ride", "error");
                        }
                    });
            });

            $(document).on('click', '.ride_completed', function (e) {
                e.preventDefault();
                var id = $(this).attr('rideid');
                var update_status = $(this).attr('updatestatus');
                var txt, title, status, url;
                status = "Completed";

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
                            $.ajax({
                                type: 'get',
                                url: url,
                                data: {id: id, update_status: update_status},
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
                                        $("#ride_status_change").addClass("completed");
                                        $("#ride_status_change").empty();
                                        $("#ride_status_change").text("Completed");
                                        swal("Success", "Ride status update successfully", "success");

                                    }else {
                                        swal("Warning", result.message, "warning");
                                        console.log(result);
                                    }
                                    // else {
                                    //     swal("Cancelled", "Ride Status Not Updated Something Wrong!", "error");
                                    //     console.log(result);
                                    // }
                                }
                            })
                        } else {
                            swal("Cancelled", "Ride Status Not Updated", "error");
                        }
                    });
            });
        </script>
    @endif
    <script>
        function initMap() {
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: {lat: 14.5995, lng: 120.9842}
            });
            directionsDisplay.setMap(map);
            calculateAndDisplayRoute(directionsService, directionsDisplay);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            directionsService.route({
                origin: "{{ isset($ride_Details)? $ride_Details->pickup_address : '' }}",
                destination: "{{ isset($ride_Details)? $ride_Details->destination_address : '' }}",
                travelMode: 'DRIVING'
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ (isset($general_settings) && ($general_settings->map_key != Null))? $general_settings->map_key : 0 }}&callback=initMap"
            async defer></script>
@endsection

