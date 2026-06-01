@extends('admin.layout.super_admin')
@section('title')
    @if(isset($pages))
        {{ ucwords(strtolower(str_replace('-',' ',$pages->name))) }}
    @else
        Page
    @endif
@endsection
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive/responsive.bootstrap4.min.css')}}">
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
                                    <h5>
                                        @if(isset($pages))
                                            {{ ucwords(strtolower(str_replace('-',' ',$pages->name))) }}
                                        @else
                                            Page
                                        @endif
                                    </h5>
                                    <span>
                                        @if(isset($pages))
                                            {{ ucwords(strtolower(str_replace('-',' ',$pages->name))) }}
                                        @else
                                            Page
                                        @endif
                                    </span>
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
                            <div style="padding: 8px;"></div>
                            <div class="card-block">
                                <form id="main" method="post" action="{{ route('post:admin:update_pages') }}" enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    @if(isset($pages))
                                        <input type="hidden" name="name" value="{{$pages->name}}">
                                    @endif
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <textarea id="textarea" name="description" placeholder="Page Description"
                                                      class="form-control">{{ (isset($pages)) ? $pages->description : old('description')}}</textarea>
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary m-b-0">Save</button>
                                        </div>
                                    </div>
                                </form>
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
    <script src="https://tinymce.cachefly.net/4.0/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            height: "280",
            plugins: [
                'code image spellchecker',
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code',
                'insertdatetime media nonbreaking save table contextmenu directionality',
            ],
            toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print media | preview | code | ltr rtl',
        });
    </script>
@endsection
