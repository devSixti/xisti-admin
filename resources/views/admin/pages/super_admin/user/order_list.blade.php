@extends('admin.layout.super_admin')
@section('title', __('admin.pages.customer_order_list'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <style>
        .customer-details td, .customer-details th {
            padding: 5px;
        }

        .customer-order {
            padding: 0;
            width: 100%;
            float: left;
        }

        .transport-1 {
            color: #4dc271;
        }

        .transport-2 {
            color: #aa5de2;
        }

        .transport-1 img, .transport-2 img {
            width: 20px;
            height: 20px;
        }

        .transport-1 b, .transport-2 b {
            font-size: 14px;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-list bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>{{ __('admin.pages.customer_order_list') }}</h5>
                                    <span>{{ \App\Helpers\AdminUi::pageSubtitle('customer_order_list') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="pcoded-inner-content">
            <div class="main-body col-md-12 customer-order">
                <div class="page-wrapper">
                    <!-- Page body start -->
                    <div class="page-body">
                        <div class="card">
                            <div class="card-header">
                                <h5>Customer Profile Details</h5>
                            </div>
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="customer-details">
                                            <tr>
                                                <th>{{ __('admin.columns.name') }}</th>
                                                <td>{{ isset($user_details)? ucwords(strtolower($user_details->first_name." ".$user_details->last_name)) : "----" }}</td>
                                            </tr>
                                            <tr>
                                                <th>Last Active</th>
                                                <td>{{ isset($user_details)? date("d F, Y h:i A", strtotime($user_details->updated_at)) : "----" }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="customer-details">
                                            <tr>
                                                <th>{{ __('admin.columns.email') }}</th>
                                                <td>{{ isset($user_details)? $user_details->email : "----" }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('admin.forms.contact_no') }}</th>
                                                <td>{{ isset($user_details)? $user_details->contact_number : "----" }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page body end -->
            </div>
        </div>
        <div class="pcoded-inner-content" style="clear: left">
            <div class="main-body">
                <div class="page-wrapper">
                    <!-- Page body start -->
                    <div class="page-body">
                        <div class="card">
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <ul class="nav nav-tabs md-tabs " role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active show" data-toggle="tab" href="#transport"
                                               role="tab"
                                               aria-selected="true"><i class="icofont icofont-home"></i>
                                                <h5>Transport Orders</h5>
                                            </a>
                                            <div class="slide"></div>
                                        </li>
                                    </ul>
                                    <div class="tab-content card-block">
                                        <div class="tab-pane active show" id="transport" role="tabpanel">
                                            <table id="new-cons-1" class="table table-striped table-bordered nowrap"
                                                   style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>{{ __('admin.common.no') }}</th>
                                                    <th>Order No.</th>
                                                    <th>{{ __('admin.columns.service_name') }}</th>
                                                    <th>Total Amount</th>
                                                    <th>{{ __('admin.columns.payment_type') }}</th>
                                                    <th>Order Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(isset($transport_order_list))
                                                    @foreach($transport_order_list as $key => $order)
                                                        <tr>
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ $order->order_no }}</td>
                                                            <td class="{{$order->service_cat_type == 1 ? "transport-1" : ($order->service_cat_type == 5 ? "transport-2" : "")}}">
                                                                <img src="{{ asset('/assets/images/service-category/'.$order->service_cat_icon)  }}">
                                                                <b>{{ $order->service_cat_name }}</b>
                                                            </td>
                                                            <td class=""> <span class="currency"></span> {{ $order->total_pay }}</td>
                                                            <td>
                                                                {{ $order->payment_type }}
                                                                @if($order->payment_type == 1)
                                                                    {{ ucwords("cash") }}
                                                                @elseif($order->payment_type == 2)
                                                                    {{ ucwords("card") }}
                                                                @elseif($order->payment_type == 3)
                                                                    {{ ucwords("wallet") }}
                                                                @endif
                                                            </td>
                                                            @if(isset($transport_ride_status))
                                                                <?php
                                                                if ($order->status == 0) {
                                                                    $bg_status_class = "pending";
                                                                } elseif (in_array($order->status, [1, 2])) {
                                                                    $bg_status_class = "approved";
                                                                } elseif (in_array($order->status, [3, 4, 10])) {
                                                                    $bg_status_class = "cancelled";
                                                                } elseif (in_array($order->status, [5])) {
                                                                    $bg_status_class = "processing";
                                                                } elseif (in_array($order->status, [6, 7, 8])) {
                                                                    $bg_status_class = "ongoing";
                                                                } elseif ($order->status == 9) {
                                                                    $bg_status_class = "completed";
                                                                } else {
                                                                    $bg_status_class = "";
                                                                }
                                                                ?>
                                                            @else
                                                                <?php
                                                                $bg_status_class = "";
                                                                ?>
                                                            @endif
                                                            <td class="icon-url-link">
                                                                {{--<a href="{{ route('get:admin:country_city_list',"china") }}">--}}
                                                                <div class="data-table-main icon-list-demo order-status">
                                                                    <span class="{{ $bg_status_class }}">{{ isset($transport_ride_status) ? str_replace('-',' ',ucwords(trim($transport_ride_status[$order->status]))) : '' }}</span>
                                                                </div>
                                                                {{--</a>--}}
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
    <script>
        $('#new-cons-1').DataTable({
            responsive: true
        });
    </script>
@endsection

