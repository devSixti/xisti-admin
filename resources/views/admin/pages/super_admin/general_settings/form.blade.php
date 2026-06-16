@extends('admin.layout.super_admin')
@section('title')
    Site Setting
@endsection
@section('page-css')
    <style>
        .image {
            padding-top: 0;
        }

        #image-preview-1, #image-preview-2 {
            border: 1px solid #9e9e9e;
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            color: #ecf0f1;
            cursor: pointer;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        #image-preview-1 input, #image-preview-2 input {
            line-height: 200px;
            font-size: 200px;
            position: absolute;
            opacity: 0;
            z-index: 10;
        }

        #image-preview-1 label, #image-preview-2 label {
            position: absolute;
            z-index: 5;
            opacity: 0.8;
            cursor: pointer !important;
            background-color: #000000d1;
            color: white;
            width: 200px;
            height: 50px;
            font-size: 20px;
            line-height: 50px;
            text-transform: uppercase;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            text-align: center;
        }
        .pencil-image-card {
            left: auto !important;
            bottom: auto !important;
            width: fit-content !important;
            padding-right: 3% !important;
        }

        .res-banner-label {
            margin-top: -20px;
            margin-bottom: 15px;
        }

        @if(isset($general_settings) && $general_settings->website_logo != Null)
            #image-preview-1 {
            background: url({{ asset('/assets/images/website-logo-icon/'.$general_settings->website_logo) }}) no-repeat;
            width: 100%;
            height: 200px;
            background-size: 350px 115px;
            /*background-size: cover;*/
            background-position: center;
        }

        @endif
@if(isset($general_settings) && $general_settings->website_favicon != Null)
#image-preview-2 {
            background: url({{ asset('/assets/images/website-logo-icon/'.$general_settings->website_favicon) }}) no-repeat;
            width: 100%;
            height: 200px;
            /*background-size: cover;*/
            background-size: 100px 100px;
            /*background-attachment: fixed;*/
            background-position: center;
        }

        @endif

        .form-group {
            margin-bottom: 10px;
        }

        /*Toll charge & Average Speed hide*/
        .avg_speed_for_eta_hide , .toll_charge_hide{
            display: none;
        }
    </style>
@endsection
@section('page-content')

    <div class="pcoded-content">
        <div class="page-header card">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="feather icon-edit-1 bg-c-blue"></i>
                        <div class="d-inline">
                            <h5>Site Setting</h5>
                            <span>@if(!isset($general_settings))Add @else Edit @endif Site Setting</span>
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
                        <form id="main" method="post" action="{{route('post:admin:update_general_setting')}}"
                              enctype="multipart/form-data">
                            {{csrf_field() }}
                            @if(isset($general_settings))
                                <input type="hidden" name="id" value="{{$general_settings->id}}">
                            @endif

                            <div class="card">
                                <div class="card-header">
                                    <h5>@if(!isset($general_settings))Add @else Edit @endif Site Setting</h5>
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Website Name:<sup
                                                        class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control" name="website_name" required
                                                           id="website_name" placeholder="Website Name"
                                                           value="{{ (isset($general_settings)) ? $general_settings->website_name : old('website_name') }}">
                                                    <span class="error">{{ $errors->first('website_name') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <label class="col-sm-12 col-form-label image">Website
                                                            Logo:</label>
                                                        <div class="col-sm-12">
                                                            <div id="image-preview-1">
                                                                @if(isset($general_settings))
                                                                    <label for="image-upload-1" class="bg-transparent pencil-image-card" id="image-label">
                                                                        <i class="fas text-dark fa-pencil-alt"></i>
{{--                                                                        Change Logo--}}
                                                                    </label>
                                                                    {{--<img id="pre-img-res" src="{{ asset('restaurant/'.$restaurant->image) }}">--}}
                                                                @else
                                                                    <label for="image-upload-1" id="image-label">Upload
                                                                        Logo</label>
                                                                @endif
                                                                <input type="file" id="image-upload-1"
                                                                       name="website_logo"/>
                                                            </div>
                                                            <span class="note">[Note: Upload only png file dimension between 300*300 to 500*500 & max size 100kb.]</span>
                                                            <span
                                                                class="error">{{ $errors->first('website_logo') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <label class="col-sm-12 col-form-label image">Website
                                                            Favicon:</label>
                                                        <div class="col-sm-12">
                                                            <div id="image-preview-2">
                                                                @if(isset($general_settings))
                                                                    <label for="image-upload-2" class="bg-transparent pencil-image-card" id="image-label">
{{--                                                                        Change Icon--}}
                                                                        <i class="fas text-dark fa-pencil-alt"></i>
                                                                    </label>
                                                                    {{--<img id="pre-img-res" src="{{ asset('restaurant/'.$restaurant->image) }}">--}}
                                                                @else
                                                                    <label for="image-upload-2" id="image-label">Upload
                                                                        Icon</label>
                                                                @endif
                                                                <input type="file" id="image-upload-2"
                                                                       name="website_favicon"/>
                                                            </div>
                                                            <span class="note">[Note: Upload only ico file dimension max 50*50 & max size 100kb.]</span>
                                                            <span
                                                                class="error">{{ $errors->first('website_favicon') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Address:</label>
                                                <div class="col-sm-12">
                                                    <textarea name="address" id="address"
                                                              class="form-control"
                                                              placeholder="address">{{ (isset($general_settings)) ? $general_settings->address : old('address') }}</textarea>
                                                    <span class="error">{{ $errors->first('address') }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Admin Receive
                                                            Email:</label>
                                                        <div class="col-sm-12">
                                                            <input type="email" class="form-control"
                                                                   name="send_receive_email"
                                                                   id="send_receive_email"
                                                                   placeholder="Admin Receive Email"
                                                                   value="{{ (isset($general_settings)) ? App\Models\User::Email2Stars($general_settings->send_receive_email) : old('send_receive_email') }}">
                                                            <span
                                                                class="error">{{ $errors->first('send_receive_email') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Email:</label>
                                                        <div class="col-sm-12">
                                                            <input type="email" class="form-control" name="email"
                                                                   id="email" placeholder="Email"
                                                                   value="{{ (isset($general_settings)) ? App\Models\User::Email2Stars($general_settings->email) : old('email') }}">
                                                            <span class="error">{{ $errors->first('email') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Contact No:</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" name="contact_no"
                                                                   id="contact_no" placeholder="Contact No"
                                                                   value="{{ (isset($general_settings)) ? App\Models\User::ContactNumber2Stars($general_settings->contact_no) : old('contact_no') }}">
                                                            <span
                                                                class="error">{{ $errors->first('contact_no') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Copy Right
                                                            Content:</label>
                                                        <div class="col-sm-12">
                                                            <input type="text" class="form-control" name="copy_right"
                                                                   id="copy_right" placeholder="Copy Right Content"
                                                                   value="{{ (isset($general_settings)) ? $general_settings->copy_right : old('copy_right') }}">
                                                            <span
                                                                class="error">{{ $errors->first('copy_right') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Social Link</h5>
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Facebook Link:</label>
                                                        <div class="col-sm-12">
                                                            <input type="url" class="form-control" name="facebook_link"
                                                                   id="facebook_link" placeholder="Facebook Link"
                                                                   value="{{ (isset($general_settings)) ? $general_settings->facebook_link : old('facebook_link') }}">
                                                            <span
                                                                class="error">{{ $errors->first('facebook_link') }}</span>
                                                        </div>
                                                    </div>
    <!--                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Twitter Link:</label>
                                                        <div class="col-sm-12">
                                                            <input type="url" class="form-control" name="twitter_link"
                                                                   id="twitter_link" placeholder="Twitter Link"
                                                                   value="{{ (isset($general_settings)) ? $general_settings->twitter_link : old('twitter_link') }}">
                                                            <span
                                                                class="error">{{ $errors->first('twitter_link') }}</span>
                                                        </div>
                                                    </div>-->
                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Linkedin Link:</label>
                                                        <div class="col-sm-12">
                                                            <input type="url" class="form-control" name="linkedin_link"
                                                                   id="linkedin_link" placeholder="Linkedin Link"
                                                                   value="{{ (isset($general_settings)) ? $general_settings->linkedin_link : old('linkedin_link') }}">
                                                            <span
                                                                class="error">{{ $errors->first('linkedin_link') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">

                                                    <div class="form-group row">
                                                        <label class="col-sm-12 col-form-label">Instagram Link:</label>
                                                        <div class="col-sm-12">
                                                            <input type="url" class="form-control" name="instagram_link"
                                                                   id="instagram_link" placeholder="Instagram Link"
                                                                   value="{{ (isset($general_settings)) ? $general_settings->instagram_link : old('instagram_link') }}">
                                                            <span
                                                                class="error">{{ $errors->first('instagram_link') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>App-Store and Play-Store Link</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">User Playstore
                                                    Link:</label>
                                                <div class="col-sm-12">
                                                    <input type="url" class="form-control"
                                                           name="user_playstore_link"
                                                           id="user_playstore_link"
                                                           placeholder="User Playstore Link"
                                                           value="{{ (isset($general_settings)) ? $general_settings->user_playstore_link : old('user_playstore_link') }}">
                                                    <span
                                                        class="error">{{ $errors->first('user_playstore_link') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">User Appstore
                                                    Link:</label>
                                                <div class="col-sm-12">
                                                    <input type="url" class="form-control"
                                                           name="user_appstore_link"
                                                           id="user_appstore_link"
                                                           placeholder="User Appstore Link"
                                                           value="{{ (isset($general_settings)) ? $general_settings->user_appstore_link : old('user_appstore_link') }}">
                                                    <span
                                                        class="error">{{ $errors->first('user_appstore_link') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5>User Refer Discount</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Used User
                                                    Discount:</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="used_user_discount" step="0.01"
                                                           min="0"
                                                           id="used_user_discount"
                                                           placeholder="Used User Discount"
                                                           value="{{ (isset($general_settings)) ? $general_settings->used_user_discount : old('used_user_discount') }}">
                                                    <span
                                                        class="error">{{ $errors->first('used_user_discount') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Refer User
                                                    Discount:</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           min="0"
                                                           name="refer_user_discount" step="0.01"
                                                           id="refer_user_discount"
                                                           placeholder="Refer User Discount"
                                                           value="{{ (isset($general_settings)) ? $general_settings->refer_user_discount : old('refer_user_discount') }}">
                                                    <span
                                                        class="error">{{ $errors->first('refer_user_discount') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Used User Discount
                                                    Type:</label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="used_user_discount_type"
                                                            id="used_user_discount_type"
                                                            placeholder="Used User Discount Type"
                                                            value="{{ (isset($general_settings)) ? $general_settings->used_user_discount_type : old('used_user_discount_type') }}">
                                                        <option value="">Select Used User Discount Type</option>
                                                        <option
                                                            value="1" {{ (isset($general_settings)) ? ($general_settings->used_user_discount_type == 1 ? "selected" : "") : "" }}>
                                                            Amount
                                                        </option>
                                                        <option
                                                            value="2" {{ (isset($general_settings)) ? ($general_settings->used_user_discount_type == 2 ? "selected" : "") : "" }}>
                                                            Percentage
                                                        </option>
                                                    </select>

                                                    <span
                                                        class="error">{{ $errors->first('used_user_discount_type') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Refer User Discount
                                                    Type:</label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="refer_user_discount_type"
                                                            id="refer_user_discount_type"
                                                            placeholder="Refer User Discount Type">
                                                        <option value="">Select Refer User Discount Type
                                                        </option>
                                                        <option
                                                            value="1" {{ (isset($general_settings)) ? ($general_settings->refer_user_discount_type == 1 ? "selected" : "") : "" }}>
                                                            Amount
                                                        </option>
                                                        <option
                                                            value="2" {{ (isset($general_settings)) ? ($general_settings->refer_user_discount_type == 2 ? "selected" : "") : "" }}>
                                                            Percentage
                                                        </option>
                                                    </select>

                                                    <span
                                                        class="error">{{ $errors->first('refer_user_discount_type') }}</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- code for Payment Methods --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Payment Methods</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Cash<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="cash_payment"
                                                            required id="cash_payment">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->cash_payment == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->cash_payment == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('cash_payment') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Wallet<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="wallet_payment"
                                                            required id="wallet_payment">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->wallet_payment == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->wallet_payment == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('wallet_payment') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Card<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="card_payment"
                                                            required id="card_payment">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->card_payment == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->card_payment == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('card_payment') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- code for Autosettlement Module --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Auto Settlement Module</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label" id="autosettle_Module_label">Auto Settle Module</label>
                                                <div class="col-sm-12">
                                                    <select class="js-example-basic-single col-sm-12 form-control" name="autosettle_Module" id="autosettle_Module">
                                                        <option style="padding-left: 8px;" value="0" <?php echo($general_settings->auto_settle_wallet == 0) ? 'selected="selected"':"" ?>>Off</option>
                                                        <option value="1" <?php echo($general_settings->auto_settle_wallet == 1) ? 'selected="selected"':"" ?>>On</option>
                                                    </select>
                                                    @if($errors->has('autosettle_Module'))
                                                        <div class="error">{{ $errors->first('autosettle_Module') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" id="driver_min_amount_for_change" <?php echo($general_settings->auto_settle_wallet == 1) ? '': 'hidden' ?>>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Minimum wallet Required Amount for Request (Driver):</label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control"  min="0" name="driver_min_amount"
                                                           id="driver_min_amount" required
                                                           placeholder="Minimum wallet Amount"
                                                           value="{{ (isset($general_settings)) ? $general_settings->driver_min_amount : old('driver_min_amount') }}">
                                                    <span
                                                        class="error">{{ $errors->first('driver_min_amount') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" id="min_cashout_for_change" <?php echo($general_settings->auto_settle_wallet == 1) ? '': 'hidden' ?>>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Minimum Cashout:</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="min_cashout"
                                                           min="0"
                                                           id="min_cashout"
                                                           placeholder="Min Cashout"
                                                           value="{{ (isset($general_settings)) ? $general_settings->min_cashout : old('min_cashout') }}">
                                                    <span
                                                        class="error">{{ $errors->first('min_cashout') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" id="max_cashout_for_change" <?php echo($general_settings->auto_settle_wallet == 1) ? '': 'hidden' ?>>
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Maximum Cashout:</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="max_cashout" min="0"
                                                           id="max_cashout"
                                                           placeholder="Max Cashout"
                                                           value="{{ (isset($general_settings)) ? $general_settings->max_cashout : old('max_cashout') }}">
                                                    <span class="error">{{ $errors->first('max_cashout') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- code for social login on/off --}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Social Login</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Google Login<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="is_google_login"
                                                            required id="is_google_login">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->is_google_login == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->is_google_login == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('is_google_login') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Facebook Login<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="is_facebook_login"
                                                            required id="is_facebook_login">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->is_facebook_login == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->is_facebook_login == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('is_facebook_login') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Apple Login<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="is_apple_login"
                                                            required id="is_apple_login">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->is_apple_login == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->is_apple_login == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('is_apple_login') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Bio-Metric Login<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="is_finger_login"
                                                            required id="is_apple_login">
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->is_finger_login == 1 ? "selected" : '' }}>
                                                            On
                                                        </option>
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->is_finger_login == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('is_finger_login') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- End code for social login on/off --}}

                            {{--Document Expiry Warnings--}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Document Expiry Warnings</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Document Expiry Warning 1(in days) :</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="doc_expiry_warning_one"
                                                           min="1"
                                                           id="doc_expiry_warning_one"
                                                           placeholder="Document Expiry Warning 1"
                                                           value="{{ (isset($general_settings)) ? $general_settings->doc_expiry_warning_one : old('doc_expiry_warning_one') }}">
                                                    <span
                                                        class="error">{{ $errors->first('doc_expiry_warning_one') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Document Expiry Warning 2(in days) :</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="doc_expiry_warning_two"
                                                           min="1"
                                                           id="doc_expiry_warning_two"
                                                           placeholder="Document Expiry Warning 2"
                                                           value="{{ (isset($general_settings)) ? $general_settings->doc_expiry_warning_two : old('doc_expiry_warning_two') }}">
                                                    <span
                                                        class="error">{{ $errors->first('doc_expiry_warning_two') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Document Expiry Warning 3 (in days) :</label>
                                                <div class="col-sm-12">
                                                    <input type="number" class="form-control"
                                                           name="doc_expiry_warning_three" min="1"
                                                           id="doc_expiry_warning_three"
                                                           placeholder="Document Expiry Warning 3"
                                                           value="{{ (isset($general_settings)) ? $general_settings->doc_expiry_warning_three : old('doc_expiry_warning_three') }}">
                                                    <span class="error">{{ $errors->first('doc_expiry_warning_three') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- code for dynamic Toll charge module 0 - off , 1 - driver will give the final charge , 2 - driver will give no of tolls & charge per toll is decided by admin--}}
                            <div class="card">
                                <div class="card-header">
                                    <h5>Toll Charge</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Toll Charge<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <select type="text" class="form-control"
                                                            name="is_toll_module"
                                                            required id="is_toll_module">
                                                        <option
                                                            value="0" {{ (isset($general_settings)) && $general_settings->is_toll_module == 0 ? "selected" : '' }}>
                                                            Off
                                                        </option>
                                                        <option
                                                            value="1" {{ (isset($general_settings)) && $general_settings->is_toll_module == 1 ? "selected" : '' }}>
                                                            Final Toll Charge given by driver
                                                        </option>
                                                        <option
                                                            value="2" {{ (isset($general_settings)) && $general_settings->is_toll_module == 2 ? "selected" : '' }}>
                                                            No. of Tolls given by Driver & Charge per Toll decided by Admin
                                                        </option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('is_toll_module') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 toll_per_charge {{ ($general_settings->is_toll_module != 2) ? 'toll_charge_hide' : '' }}">
                                            <div class="form-group row">
                                                <label class="col-sm-12 col-form-label">Toll Charge(charge per toll)<sup class="error">*</sup></label>
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control"
                                                           name="charge_per_toll" required
                                                           id="charge_per_toll"
                                                           placeholder="Toll Charge(charge per toll)"
                                                           value="{{ (isset($general_settings)) ? $general_settings->charge_per_toll : old('charge_per_toll') }}">
                                                    <span
                                                        class="error">{{ $errors->first('charge_per_toll') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card">
                                <div class="card-header">
                                    <h5>Mobile app — fares and trips</h5>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label class="col-form-label">Driver Price Suggestion<sup class="error">*</sup></label>
                                            <input type="text" class="form-control" name="driver_price_suggestion" required
                                                   id="driver_price_suggestion" min="1"
                                                   value="{{ $general_settings->driver_price_suggestion ?? old('driver_price_suggestion', 1) }}">
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="col-form-label">Fare negotiation step (COP)</label>
                                            <input type="number" class="form-control" name="fare_negotiation_step" min="1"
                                                   value="{{ $general_settings->fare_negotiation_step ?? old('fare_negotiation_step', 500) }}">
                                            <small class="text-muted">e.g. 500 for +/- on fare offers (COP).</small>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="col-form-label">VAT on commission (%)</label>
                                            <input type="number" step="0.01" class="form-control" name="vat_rate_on_commission" min="0"
                                                   value="{{ $general_settings->vat_rate_on_commission ?? old('vat_rate_on_commission', 19) }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <label class="col-form-label">Driver may cancel until status</label>
                                            <input type="number" class="form-control" name="driver_cancel_until_status" min="0" max="9"
                                                   value="{{ $general_settings->driver_cancel_until_status ?? old('driver_cancel_until_status', 3) }}">
                                            <small class="text-muted">3 = arrived at pickup; 4 = cancelled (B4).</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border-info">
                                <div class="card-header bg-light">
                                    <h5>XISTI mobile — modos opcionales</h5>
                                    <span class="text-muted">XISTI (Medellín): deja Expreso desmarcado hasta definir rutas intermunicipales. Encomiendas suele ir activo en lanzamiento urbano. No confundir con ZIMO (flags municipales en su propia instancia).</span>
                                </div>
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-4">
                                            <div class="checkbox-fade fade-in-primary">
                                                <label>
                                                    <input type="checkbox" name="enable_expreso_mobile" value="1"
                                                        @checked(($general_settings->enable_expreso_mobile ?? 0) == 1)>
                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                    <span>Expreso (passenger + driver)</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="checkbox-fade fade-in-primary">
                                                <label>
                                                    <input type="checkbox" name="enable_encomiendas_mobile" value="1"
                                                        @checked(($general_settings->enable_encomiendas_mobile ?? 0) == 1)>
                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                    <span>Encomiendas (passenger + driver)</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="checkbox-fade fade-in-primary">
                                                <label>
                                                    <input type="checkbox" name="require_courier_package_dimensions_mobile" value="1"
                                                        @checked(($general_settings->require_courier_package_dimensions_mobile ?? 0) == 1)>
                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                    <span>Require courier package dimensions</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="checkbox-fade fade-in-primary">
                                                <label>
                                                    <input type="checkbox" name="enable_xisti_new_home_layout" value="1"
                                                        @checked(($general_settings->enable_xisti_new_home_layout ?? 1) == 1)>
                                                    <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                    <span>New passenger home layout (map + sheet)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                $destinationPaymentCatalog = \App\Helpers\DestinationPaymentHelper::catalog($general_settings ?? null);
                            @endphp
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Métodos de pago en destino (app móvil)</h5>
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-destination-payment">+ Agregar método</button>
                                </div>
                                <div class="card-block">
                                    <p class="text-muted">Esta lista aparece en la app al reservar envíos/encomiendas. Código: minúsculas, sin espacios (ej. <code>nequi</code>).</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="destination-payment-methods-table">
                                            <thead>
                                            <tr>
                                                <th style="width:22%">Código</th>
                                                <th style="width:34%">Etiqueta (ES)</th>
                                                <th style="width:34%">Etiqueta (EN)</th>
                                                <th style="width:10%"></th>
                                            </tr>
                                            </thead>
                                            <tbody id="destination-payment-methods-body">
                                            @foreach($destinationPaymentCatalog as $row)
                                                <tr class="destination-payment-row">
                                                    <td><input type="text" class="form-control" name="destination_payment_code[]" value="{{ $row['code'] }}" required maxlength="32" pattern="[a-z][a-z0-9_]*"></td>
                                                    <td><input type="text" class="form-control" name="destination_payment_label_es[]" value="{{ $row['label_es'] }}" required></td>
                                                    <td><input type="text" class="form-control" name="destination_payment_label_en[]" value="{{ $row['label_en'] }}" required></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-destination-payment" title="Quitar">×</button></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <details class="mt-2">
                                        <summary class="text-muted">Avanzado: JSON manual</summary>
                                        <textarea class="form-control mt-2" name="destination_payment_methods" rows="4"
                                                  placeholder='[{"code":"cash","label_es":"Efectivo","label_en":"Cash"}]'>{{ $general_settings->destination_payment_methods ?? '' }}</textarea>
                                        <small class="text-muted">Solo se usa si la tabla queda vacía al guardar.</small>
                                    </details>
                                </div>
                            </div>
                            <script>
                                (function () {
                                    const body = document.getElementById('destination-payment-methods-body');
                                    const addBtn = document.getElementById('btn-add-destination-payment');
                                    if (!body || !addBtn) return;

                                    function bindRemove(btn) {
                                        btn.addEventListener('click', function () {
                                            const rows = body.querySelectorAll('.destination-payment-row');
                                            if (rows.length <= 1) {
                                                rows[0].querySelectorAll('input').forEach(function (input) { input.value = ''; });
                                                return;
                                            }
                                            btn.closest('tr').remove();
                                        });
                                    }

                                    function addRow(code, labelEs, labelEn) {
                                        const tr = document.createElement('tr');
                                        tr.className = 'destination-payment-row';
                                        tr.innerHTML = '<td><input type="text" class="form-control" name="destination_payment_code[]" maxlength="32" pattern="[a-z][a-z0-9_]*" required></td>' +
                                            '<td><input type="text" class="form-control" name="destination_payment_label_es[]" required></td>' +
                                            '<td><input type="text" class="form-control" name="destination_payment_label_en[]" required></td>' +
                                            '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-destination-payment" title="Quitar">×</button></td>';
                                        body.appendChild(tr);
                                        tr.querySelector('[name="destination_payment_code[]"]').value = code || '';
                                        tr.querySelector('[name="destination_payment_label_es[]"]').value = labelEs || '';
                                        tr.querySelector('[name="destination_payment_label_en[]"]').value = labelEn || '';
                                        bindRemove(tr.querySelector('.btn-remove-destination-payment'));
                                    }

                                    body.querySelectorAll('.btn-remove-destination-payment').forEach(bindRemove);
                                    addBtn.addEventListener('click', function () { addRow('', '', ''); });
                                })();
                            </script>



                            {{--                            <div class="card">--}}
{{--                                <div class="card-header">--}}
{{--                                    <h5>Delivery Admin Commission</h5>--}}
{{--                                </div>--}}
{{--                                <div class="card-block">--}}
{{--                                    <div class="form-group row">--}}
{{--                                        <label class="col-sm-12 col-form-label">Delivery Commision (in--}}
{{--                                            %):</label>--}}
{{--                                        <div class="col-sm-12">--}}
{{--                                            <input type="number" class="form-control"--}}
{{--                                                   name="delivery_commission"--}}
{{--                                                   required step="0.01"--}}
{{--                                                   id="delivery_commission"--}}
{{--                                                   placeholder="Delivery Commission"--}}
{{--                                                   value="{{ (isset($general_settings)) ? $general_settings->delivery_commission : old('delivery_commission') }}">--}}
{{--                                            <span class="error">{{ $errors->first('delivery_commission') }}</span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="card">--}}
{{--                                <div class="card-header">--}}
{{--                                    <h5>On-Demand Start Service Time</h5>--}}
{{--                                </div>--}}
{{--                                <div class="card-block">--}}
{{--                                    <div class="form-group row">--}}
{{--                                        <div class="col-sm-12">--}}
{{--                                            Provider can start the service before <input type="number" class="form-control"--}}
{{--                                                                            name="on_demand_start_service_time" required--}}
{{--                                                                            id="on_demand_start_service_time"--}}
{{--                                                                            placeholder="Time"--}}
{{--                                                                                         value="{{ (isset($general_settings)) ? $general_settings->on_demand_start_service_time : old('on_demand_start_service_time') }}"--}}
{{--                                                                            style="width: 30%; display: inline"> minutes--}}
{{--                                            of requested time.--}}
{{--                                            <span--}}
{{--                                                class="error">{{ $errors->first('on_demand_start_service_time') }}</span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="card">--}}
{{--                                <div class="card-header">--}}
{{--                                    <h5>Driver Algorithm</h5>--}}
{{--                                </div>--}}
{{--                                <div class="card-block">--}}
{{--                                    <div class="form-group row">--}}
{{--                                        <div class="col-sm-6">--}}
{{--                                            <div class="form-group row">--}}
{{--                                                <label class="col-sm-12 col-form-label">Driver Algorithm</label>--}}
{{--                                                <div class="col-sm-12">--}}
{{--                                                    <select type="text" class="form-control"--}}
{{--                                                            name="driver_algorithm"--}}
{{--                                                            required id="driver_algorithm">--}}
{{--                                                        <option--}}
{{--                                                            value="0" {{ (isset($general_settings)) && $general_settings->driver_algorithm == 0 ? "selected" : '' }}>--}}
{{--                                                            Competitive algorithm--}}
{{--                                                        </option>--}}
{{--                                                        <option--}}
{{--                                                            value="1" {{ (isset($general_settings)) && $general_settings->driver_algorithm == 1 ? "selected" : '' }}>--}}
{{--                                                            Nearest 1St--}}
{{--                                                        </option>--}}
{{--                                                    </select>--}}
{{--                                                    <span class="error">{{ $errors->first('driver_algorithm') }}</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-sm-6">--}}
{{--                                            <div class="form-group row">--}}
{{--                                                <label class="col-sm-12 col-form-label">User Request--}}
{{--                                                    Timeout(Seconds):</label>--}}
{{--                                                <div class="col-sm-12">--}}
{{--                                                    <input type="text" class="form-control"--}}
{{--                                                           name="user_timeout"--}}
{{--                                                           id="user_timeout"--}}
{{--                                                           placeholder="User timeout in seconds"--}}
{{--                                                           value="{{ (isset($general_settings)) ? $general_settings->user_timeout : old('user_timeout') }}">--}}
{{--                                                    <span--}}
{{--                                                        class="error">{{ $errors->first('user_timeout') }}</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="card">
                                <div class="card-block">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary m-b-0 buttonloader">Save</button>
                                        </div>
                                    </div>
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
    <script src="{{ asset('assets/js/upload_image.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.validator.addMethod("greaterThanMin", function(value, element) {
                var minCashout = parseFloat($("#min_cashout").val());
                var maxCashout = parseFloat(value);
                return !isNaN(maxCashout) && !isNaN(minCashout) && maxCashout > minCashout;
            }, "Maximum Cashout must be greater than to Minimum Cashout.");

            $.validator.addMethod("threeDigitNumber",function (value, element) {
                    return this.optional(element) || /^[0-9]{1,3}$/.test(value);
                },
                "Please enter a valid 3-digit number."
            );

            $.validator.addMethod("uniqueValues", function (value, element) {
                const field1 = parseFloat($("#doc_expiry_warning_one").val());
                const field2 = parseFloat($("#doc_expiry_warning_two").val());
                const field3 = parseFloat($("#doc_expiry_warning_three").val());
                return field1 !== field2 && field1 !== field3 && field2 !== field3;
            }, "All fields must have different values.");

            $("#main").validate({
                rules: {
                    driver_min_amount: {
                        required : true,
                        min : 0.1,
                    },
                    min_cashout:{
                        min : 0.1,
                    },
                    max_cashout:{
                        greaterThanMin: true
                    },
                    doc_expiry_warning_one:{
                        min : 1,
                        threeDigitNumber:true,
                        uniqueValues: true
                    },
                    doc_expiry_warning_two:{
                        min : 1,
                        threeDigitNumber:true,
                        uniqueValues: true
                    },
                    doc_expiry_warning_three:{
                        min : 1,
                        threeDigitNumber:true,
                        uniqueValues: true
                    },
                    driver_price_suggestion:{
                        required : true,
                        min : 1,
                    },
                    charge_per_toll: {
                        required: function () {
                            return $('#is_toll_module').val() == '2';
                        },
                        min: function () {
                            return $('#is_toll_module').val() == '2' ? 1 : undefined;
                        }
                    }
                },
                messages: {
                    driver_min_amount:{
                        min:"Please enter a number greater than 0."
                    },
                    min_cashout:{
                        min:"Please enter a number greater than 0."
                    },
                    charge_per_toll:{
                        min:"Please enter a number greater than 0."
                    }
                },
                submitHandler: function(form) {
                    $('.buttonloader').attr("disabled", true);
                    $('.buttonloader').html("<i class='fa fa-spinner fa-spin'></i>");
                    form.submit();
                }
            });

            $.uploadPreview({
                input_field: "#image-upload-1", // Default: .image-upload
                preview_box: "#image-preview-1", // Default: .image-preview
                label_field: "#image-label-1", // Default: .image-label
                label_default: "Choose Image", // Default: Choose File
                label_selected: "Change Image", // Default: Change File
                no_label: false // Default: false
            });
            $.uploadPreview({
                input_field: "#image-upload-2", // Default: .image-upload
                preview_box: "#image-preview-2", // Default: .image-preview
                label_field: "#image-label-2", // Default: .image-label
                label_default: "Choose Image", // Default: Choose File
                label_selected: "Change Image", // Default: Change File
                no_label: false // Default: false
            });

            //script for toll per charge hide/show on toll_module change
            $(document).on('change', '#is_toll_module', function (e) {
                var is_toll_module = $(this).val();
                if(is_toll_module == 2){
                    $('.toll_per_charge').removeClass('toll_charge_hide');
                }else{
                    $('.toll_per_charge').addClass('toll_charge_hide');
                }
            });
        });
        $('#autosettle_Module').on('change', function() {
            if(this.value == 0){
                $('#driver_min_amount').val(0);
                $('#min_cashout').val(1);
                $('#max_cashout').val(1);
                $('#driver_min_amount_for_change').attr("hidden",true);
                $('#min_cashout_for_change').attr("hidden",true);
                $('#max_cashout_for_change').attr("hidden",true);
            }
            else{
                $('#driver_min_amount_for_change').removeAttr("hidden");
                $('#min_cashout_for_change').removeAttr("hidden");
                $('#max_cashout_for_change').removeAttr("hidden");
            }
        });
    </script>
@endsection

