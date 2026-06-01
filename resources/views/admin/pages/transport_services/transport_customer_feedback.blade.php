@extends('admin.layout.driver_service')
@section('title')
    Customer Feedback
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" media="screen"
          href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
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
                            <h5>Customer Feedback</h5>
                            <span>Feedback list</span>
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
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Customer Feedback</h5>
                                    </div>
                                    <div class="card-block">

                                        <div class="dt-responsive table-responsive">
                                            <table id="new-cons" class="table table-striped table-bordered nowrap"
                                                   style="width:100%">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Customer Name</th>
                                                    <th>Category Name</th>
                                                    <th>Rating</th>
                                                    <th>Comment</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(isset($customer_feedback))
                                                    @foreach($customer_feedback as $key => $feedback)
                                                        <tr id="remove_card_{{ $feedback->id }}">
                                                            <td>{{ $key+1 }}</td>
                                                            <td>{{ $feedback->first_name . " " .$feedback->last_name }}</td>
                                                            <td>
                                                                @if($feedback->ride_id != Null) {{ \App\Models\TransportRatings::getTransportServiceCatName($feedback->ride_id) }} @endif
                                                                @if($feedback->delivery_id != Null) {{ \App\Models\TransportRatings::getDeliveryServiceCatName($feedback->delivery_id) }} @endif
                                                                @if($feedback->rental_id != Null) {{ \App\Models\TransportRatings::getTransportRentalServiceCatName($feedback->rental_id) }} @endif
                                                            </td>
                                                            <td>{{ $feedback->rating }}</td>
                                                            <td>{{ $feedback->comment != Null ? $feedback->comment : "-----" }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="float-right">
                                            {{ $customer_feedback->links() }}
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
    {{--    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>--}}
    {{--    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>--}}
    {{--    <script src="{{ asset('assets/js/responsive/dataTables.responsive.min.js')}}"></script>--}}
    <script src="{{ asset('assets/js/responsive/responsive-custom.js')}}"></script>
@endsection

