@extends('admin.layout.super_admin')
@section('title')
    @if(isset($pages)) Edit Page @else Add Page @endif
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
                                        @if(isset($pages)) Edit  {{ ucwords(strtolower(str_replace('-',' ',$pages->name))) }} @else Add Page @endif
                                    </h5>
                                    <span>
                                        @if(isset($pages)) Edit page @else Add Page @endif
                                    </span>
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
                        <div class="card">
                            <div style="padding: 8px;"></div>
                            <div class="card-block">
                                <form id="main" method="post" action="{{ route('post:admin:update_pages') }}"
                                      enctype="multipart/form-data">
                                    {{csrf_field() }}

                                    @if(isset($pages))
                                        <input type="hidden" name="id" value="{{$pages->id}}">
                                    @endif

                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.forms.name_english') }}:<sup
                                                    class="error">*</sup></label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="name" required
                                                   id="name" placeholder="{{ __('admin.forms.page_name') }}"
                                                   {{ (isset($pages) && $pages->name != Null) ? $pages->name : "" }}
                                                   value="{{ (isset($pages)) ? $pages->name : old('name') }}">
                                            <span class="error">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    @if(isset($language_lists))
                                        @foreach($language_lists as $single_lang)
                                            @php
                                                $language_name =  isset($single_lang->language_name)?$single_lang->language_name:"";
                                                $language_code =  isset($single_lang->language_code)?$single_lang->language_code:"";
                                                $col_name = $language_code."_name";
                                            @endphp
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">{{ __('admin.forms.name_in_lang', ['lang' => $language_name]) }}:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="{{$col_name}}" required
                                                           id="{{$col_name}}" placeholder="{{ __('admin.forms.page_name_in', ['lang' => $language_name]) }}"
                                                           {{ (isset($pages) && $pages->$col_name != Null) ? $pages->name : "" }}
                                                           value="{{ (isset($pages)) ? $pages->$col_name : old($language_code) }}">
                                                    <span class="error">{{ $errors->first($language_code) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.forms.description_english') }}:<sup class="error">*</sup></label>
                                        <div class="col-sm-12">
                                            <textarea id="description1" name="description" placeholder="{{ __('admin.forms.page_description_english') }}" class="form-control description">{{ (isset($pages)) ? $pages->description : old('description')}}</textarea>
                                            <span class="error">{{ $errors->first('description') }}</span>
                                        </div>
                                    </div>

                                    @if(isset($language_lists))
                                        @php $i=1 @endphp
                                        @foreach($language_lists as $single_lang)
                                            @php
                                                $language_name =  isset($single_lang->language_name)?$single_lang->language_name:"";
                                                $language_code =  isset($single_lang->language_code)?$single_lang->language_code:"";
                                                $col_name = $language_code."_description";
                                            @endphp
                                            @php $i++ @endphp
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">{{ __('admin.forms.description_in_lang', ['lang' => $language_name]) }}:<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                            <textarea id="description{{$i}}" name="{{$col_name}}" placeholder="{{ __('admin.forms.page_description_in', ['lang' => $language_name]) }}"
                                                      class="form-control description">{{ (isset($pages)) ? $pages->$col_name : old($col_name)}}</textarea>
                                                    <span class="error">{{ $errors->first('$col_name') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary m-b-0">{{ __('admin.common.save') }}</button>
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
    <script type="text/javascript" src="{{ asset('assets/js/ckeditor.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/ckeditor-script.js')}}"></script>
@endsection
