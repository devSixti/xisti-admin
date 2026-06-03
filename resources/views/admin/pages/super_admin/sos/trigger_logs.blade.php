@extends('admin.layout.super_admin')
@section('title')
    SOS Trigger Log
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
@endsection
@section('page-content')
    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-12">
                    <div class="page-header-title">
                        <i class="feather icon-alert-triangle bg-c-red"></i>
                        <div class="d-inline">
                            <h5>SOS Trigger Log</h5>
                            <span>Auditoría de llamadas SOS iniciadas desde la app XISTI</span>
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
                                <h5>Eventos SOS</h5>
                                <a href="{{ route('get:admin:sos') }}" class="btn btn-primary m-b-0 btn-right render_link">SOS Contacts</a>
                            </div>
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="sos_trigger_logs" class="table table-striped table-bordered nowrap" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Triggered at</th>
                                            <th>User</th>
                                            <th>Role</th>
                                            <th>Ride ID</th>
                                            <th>Contact name</th>
                                            <th>Phone</th>
                                            <th>Location</th>
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
@endsection
@section('page-js')
    <script src="{{ asset('assets/js/responsive/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/js/responsive/dataTables.bootstrap4.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#sos_trigger_logs').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: "{{ route('get:admin:sos_trigger_logs_list') }}",
                columns: [
                    {data: 'id', sortable: false},
                    {data: 'triggered_at'},
                    {data: 'user', sortable: false},
                    {data: 'user_role'},
                    {data: 'ride_id'},
                    {data: 'contact_name', sortable: false},
                    {data: 'contact', sortable: false},
                    {data: 'location', sortable: false},
                ],
                order: [[1, 'desc']],
            });
        });
    </script>
@endsection
