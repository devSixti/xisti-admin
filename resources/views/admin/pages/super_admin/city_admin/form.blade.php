@extends('admin.layout.super_admin')
@section('title')
    @if(isset($admin_user)) Edit @else Add @endif City Admin
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
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="page-header-title ">
                                <i class="feather icon-list bg-c-blue"></i>
                                <div class="d-inline">
                                    <h5>
                                        @if(isset($admin_user)) Edit  {{ ucwords(strtolower(str_replace('-',' ',$admin_user->name))) }} @else Add City Admin @endif
                                    </h5>
                                    <span>
                                        @if(isset($admin_user)) Edit @else Add @endif City Admin
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
                        <form id="main" class="city_admin" method="post"
                              action="{{ route('post:admin:update_city_admin') }}" enctype="multipart/form-data">
                            {{csrf_field() }}

                            <div class="card">
                                <div class="card-header">
                                    <h5>City Admin</h5>
                                    <a href="{{ route('get:admin:city_admin_list') }}"
                                       class="btn btn-primary m-b-0 btn-right render_link">{{ __('admin.common.back') }}</a>
                                </div>
                                <div style="padding: 8px;"></div>
                                <div class="card-block">
                                    @if(isset($admin_user))
                                        <input type="hidden" name="id" value="{{$admin_user->id}}">
                                    @endif

                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.columns.name') }}:<sup class="error">*</sup></label>
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="name" required id="name"
                                                   placeholder="{{ __('admin.forms.admin_name') }}"
                                                   value="{{ (isset($admin_user)) ? $admin_user->name : old('name') }}">
                                            <span class="error">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.mfa.email_label') }}<sup class="error">*</sup></label>
                                        <div class="col-sm-12">
                                            <input type="email" class="form-control" name="email" required id="email"
                                                   pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}"
                                                   placeholder="{{ __('admin.forms.admin_email_address') }}"
                                                   value="{{ (isset($admin_user)) ? $admin_user->email : old('email') }}">
                                            <span class="error">{{ $errors->first('email') }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.forms.password') }}:
                                            @if(!isset($admin_user))
                                                <sup class="error">*</sup>
                                            @endif
                                        </label>
                                        <div class="col-sm-12">
                                            <input type="password" class="form-control" name="password"
                                                   {{(isset($admin_user) && $admin_user->password != null)?"":"required"}} id="password"
                                                   placeholder="{{ __('admin.forms.password') }}" value="">
                                            <span class="error">{{ $errors->first('password') }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">{{ __('admin.forms.select_city') }}:<sup
                                                class="error">*</sup></label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="city_id" required>
                                                <option value="" selected disabled>{{ __('admin.forms.select_city') }}</option>
                                                @if(isset($area_list))
                                                    @foreach($area_list as $key => $area_details)
                                                        <option value="{{ $area_details->id }}"
                                                                @if(isset($admin_user) && ($admin_user->area_id == $area_details->id)) selected @endif>{{ $area_details->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span class="error">{{ $errors->first('password') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Modules Permission</h5>
                                </div>
                                <div class="card-block">

                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            @if(isset($module_with_action))
                                                @foreach($module_with_action as $single_module)
                                                    @if( $single_module['is_checkbox_show'] != 1)
                                                        <div class="row">
                                                            <div class="col-sm-5"><b>{{ $single_module['name'] }}</b>
                                                            </div>
                                                        </div>
                                                        @foreach($single_module['sub_module_with_action'] as $sub_single_module)
                                                            @if($sub_single_module['module_id'] != $res_module)
                                                                <div class="row">
                                                                    <div class="col-sm-4 offset-sm-1"><i
                                                                            class="fas fa-arrow-right"
                                                                            style="font-size: 12px;"></i>
                                                                        <b>{{ $sub_single_module['name'] }} :</b></div>
                                                                    @foreach($sub_single_module['checkbox'] as $singleCheckBox)
                                                                        <div
                                                                            class="col-sm-6 admin_permission_{{$sub_single_module['module_id']}}    "
                                                                            style="{{($sub_single_module['module_id'] == $res_module && $singleCheckBox['checked']  != "checked")?"display:none":"display:block"}}">

                                                                            <input type="checkbox"
                                                                                   class="admin_permission_fld_{{$sub_single_module['module_id']}}"
                                                                                   id="{{$sub_single_module['module_id']}}_{{$singleCheckBox['id']}}"
                                                                                   name="admin_permission[{{$sub_single_module['module_id']}}][]"
                                                                                   value="{{$singleCheckBox['id']}}" {{$singleCheckBox['checked']}} >{{$singleCheckBox['name']}}

                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <hr>
                                                    @else
                                                        @if($single_module['menu_category_wise_list'] != Null)
                                                            <div class="row">
                                                                <div class="col-sm-5">
                                                                    <b>{{ $single_module['name'] }}</b></div>
                                                            </div>
                                                            @foreach($single_module['menu_category_wise_list'] as $sub_single_module)
                                                                <div class="row">
                                                                    <div class="col-sm-4 offset-sm-1"><i
                                                                            class="fas fa-arrow-right"
                                                                            style="font-size: 12px;"></i>
                                                                        <b>{{ $sub_single_module['name'] }} :</b></div>
                                                                    <div class="col-sm-6">
                                                                        @foreach($sub_single_module['checkbox'] as $singleCheckBox)
                                                                            <input type="checkbox"
                                                                                   data-cat="{{$sub_single_module['category_id']}}"
                                                                                   class="serviceCheckkbox"
                                                                                   id="{{$sub_single_module['module_id']}}_{{$singleCheckBox['id']}}" name="admin_cat_permission[{{$sub_single_module['module_id']}}][{{$sub_single_module['category_id']}}][]"
                                                                                   value="{{$singleCheckBox['id']}}" {{$singleCheckBox['checked']}} >{{$singleCheckBox['name']}}
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            <hr>
                                                        @else
                                                            <div class="row">
                                                                <div class="col-sm-5"><b>{{ $single_module['name'] }}
                                                                        :</b></div>
                                                                @foreach($single_module['checkbox'] as $singleCheckBox)
                                                                    <div
                                                                        class="col-sm-6 admin_permission_{{$single_module['module_id']}}"
                                                                        style="{{($single_module['module_id'] == $res_module && $singleCheckBox['checked'] != "checked")?"display:none":"display:block"}}">

                                                                        <input type="checkbox"
                                                                               class="admin_permission_fld_{{$single_module['module_id']}}"
                                                                               id="{{$single_module['module_id']}}_{{$singleCheckBox['id']}}"
                                                                               name="admin_permission[{{$single_module['module_id']}}][]"
                                                                               value="{{$singleCheckBox['id']}}" {{$singleCheckBox['checked']}} >{{$singleCheckBox['name']}}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <hr>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <center>
                                        <button type="submit" class="btn btn-primary m-b-0 buttonloader">{{ __('admin.common.save') }}</button>
                                    </center>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('page-js')
    <script type="text/javascript">
        /* start jquery validations for city admin */
        /* custom email validation */
        jQuery.validator.addMethod("validateEmail", function(value, element) {
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (value != ""){
                    return regex.test(value);
                }
                return true;
            }, "Please enter a valid email address."
        );
        $(".city_admin").validate({
            rules: {
                name : {
                    required : true,
                },
                email : {
                    required : true,
                    email: true,
                    validateEmail: true
                },
                city_id : {
                    required : true,
                },
            },
            submitHandler: function(form) {
                $('.buttonloader').attr("disabled", true);
                $('.buttonloader').html("<i class='fa fa-spinner fa-spin'></i>");
                form.submit();
            }
        });
        /* end jquery validations for city admin */

        $(document).ready(function (){
            var on_click_res_module = '{{ $on_click_res_module }}';
            var res_module = '{{ $res_module }}';
            $(document).on("click",'.serviceCheckkbox',function (){
                $(".admin_permission_"+res_module).css('display','none');
                $(".admin_permission_fld_"+res_module).prop('checked', false);
                $(".serviceCheckkbox:checked").each(function(){
                    curr_cat = $(this).data('cat');
                    const isInArray = on_click_res_module.includes(curr_cat);
                    if(isInArray == true){
                        $(".admin_permission_"+res_module).css('display','block');
                    }
                });
            });
            $(window).on("load", function (e) {
                $(".admin_permission_"+res_module).css('display','none');
                $(".admin_permission_fld_"+res_module).prop('checked', false);
                $(".serviceCheckkbox:checked").each(function(){
                    curr_cat = $(this).data('cat');
                    const isInArray = on_click_res_module.includes(curr_cat);
                    if(isInArray == true){
                        $(".admin_permission_"+res_module).css('display','block');
                    }
                });
            });
        });
    </script>
@endsection
