@extends('admin.layout.super_admin')
@section('title', __('admin.pages.world_currency_list'))
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
@endsection
@section('page-content')

    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header card admin-page-header">
            <div class="row align-items-end">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-dollar-sign bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>{{ __('admin.modules.world_currency') }}</h5>
                                    <span>{{ \App\Helpers\AdminUi::pageSubtitle('world_currency_list') }}</span>
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
                        <form id="main" method="post"
                              action="{{ route('post:admin:world_currency_list')}}"
                              enctype="multipart/form-data">
                            {{csrf_field() }}

                            <div class="form-group row">
                                <div class="form-group col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('admin.pages.world_currency_list') }}</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="dt-responsive table-responsive">
                                                <table class="table table-striped table-bordered nowrap" style="width:100%">
                                                    <thead>
                                                    <tr>
                                                        <th>{{ __('admin.common.no') }}</th>
                                                        <th>{{ __('admin.columns.currency_name') }}</th>
                                                        <th>{{ __('admin.columns.symbol') }}</th>
                                                        <th>{{ __('admin.forms.ratio') }}</th>
                                                        <th>{{ __('admin.columns.default') }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if(isset($currencies))
                                                        @foreach($currencies as $key => $currency)
                                                            <tr>
                                                                <td>{{ $key+1 }}</td>
                                                                <td>{{ ucwords(strtolower($currency->currency_name)) }}</td>
                                                                <td>{{ ($currency->symbol) }}</td>
                                                                @if($key == 0)
                                                                    <td>
                                                                        <input type="text" class="form-control" readonly value="{{$currency->ratio}}">
                                                                    </td>
                                                                @else
                                                                    <td>
                                                                        <input type="text" class="form-control" name="ratio[{{ $currency->id }}]" id="ratio" placeholder="{{ __('admin.forms.ratio') }}" step="0.01" required value="{{$currency->ratio}}">
                                                                    </td>
                                                                @endif
                                                                <td>{{ ($currency->default_currency == 1 ? __('admin.common.yes') : __('admin.common.no')) }}</td>
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
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <center>
                                        <button type="submit" class="btn btn-primary m-b-0">{{ __('admin.common.save') }}</button>
                                    </center>
                                </div>
                            </div>
                        </form>

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
    <script type="text/javascript">
        $(document).on('click', '.delete', function (e) {
            e.preventDefault();
            var id = $(this).attr('categoryid');
            swal({
                    title: window.adminSwal.confirmTitle,
                    text: window.adminSwal.confirmText,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: window.adminSwal.yesDelete,
                    cancelButtonText: window.adminSwal.noCancel,
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            type: 'get',
                            url: '',
                            data: {id: id},
                            success: function (result) {
                                if (result.success == true) {
                                    location.reload();
                                }
                            }
                        })
                    } else {
                        swal(window.adminSwal.cancelled, window.adminSwal.dataSafe, "error");
                    }
                });
        });
    </script>
@endsection

