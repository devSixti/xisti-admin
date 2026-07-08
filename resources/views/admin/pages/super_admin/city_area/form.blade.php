@extends('admin.layout.super_admin')
@section('title')
    @if(isset($area_details)) Edit @else Add @endif City Area
@endsection
@section('page-css')
    <style>
        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + .label {
            position: relative;
            /*margin-left: 43%;*/
            /*display: block;*/
            padding-left: 25px;
            margin-right: 10px;
            cursor: pointer;
            /*line-height: 16px;*/
            color: black;
            font-size: 14px;
            transition: all .2s ease-in-out;
            margin-bottom: 10px;
        }

        input[type="radio"] + .label:before, input[type="radio"] > .label:after {
            content: '';
            position: absolute;
            top: -1px;
            left: 0;
            width: 20px;
            height: 20px;
            text-align: center;
            color: black;
            cursor: pointer;
            border-radius: 50%;
            transition: all .3s ease;
        }

        input[type="radio"] + .label:before {
            /*box-shadow: inset 0 0 0 1px #666565, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;*/
            box-shadow: 0 0 0 0 #91DEAC, inset 0 0 0 2px #FFFFFF, inset 0 0 0 3px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
        }

        input[type="radio"] + .label:hover {
            color: #44BB6E;
        }

        input[type="radio"] + .label:hover:before {
            animation-duration: .5s;
            animation-name: change-size;
            animation-iteration-count: infinite;
            animation-direction: alternate;
            box-shadow: inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
        }

        input[type="radio"]:checked + .label:hover {
            color: #333333;
            cursor: default;
        }

        input[type="radio"]:checked + .label:before {
            animation-duration: .2s;
            animation-name: select-radio;
            animation-iteration-count: 1;
            animation-direction: Normal;
            box-shadow: inset 0 0 0 1px #44BB6E, inset 0 0 0 3px #FFFFFF, inset 0 0 0 16px #44BB6E;

        }

        @keyframes change-size {
            from {
                box-shadow: 0 0 0 0 #44BB6E, inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            to {
                box-shadow: 0 0 0 1px #44BB6E, inset 0 0 0 1px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
        }

        @keyframes select-radio {
            0% {
                box-shadow: 0 0 0 0 #91DEAC, inset 0 0 0 2px #FFFFFF, inset 0 0 0 3px #44BB6E, inset 0 0 0 16px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            90% {
                box-shadow: 0 0 0 10px #E8FFF0, inset 0 0 0 0 #FFFFFF, inset 0 0 0 1px #44BB6E, inset 0 0 0 2px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
            100% {
                box-shadow: 0 0 0 12px #E8FFF0, inset 0 0 0 0 #FFFFFF, inset 0 0 0 1px #44BB6E, inset 0 0 0 3px #FFFFFF, inset 0 0 0 16px #44BB6E;
            }
        }

        @media screen and (max-width: 576px) {
            input[type="radio"] + .label {
                margin-left: 48%;
                display: block;
            }
        }
        #searchInput{
            z-index:9999;
            position:relative;
            left: 230px;
            top:40px;
            width: 24%;
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
                            <h5>City Area</h5>
                            <span>@if(isset($area_details)) Edit @else Add @endif City Area</span>
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
                                <h5>@if(isset($area_details)) Edit @else Add @endif City Area</h5>
                                <a href="{{ route('get:admin:city_area_list') }}" class="btn btn-primary m-b-0 btn-right render_link"> Back</a>
                            </div>
                            <div class="card-block">
                                <form id="main" method="post" action="{{ route('post:admin:update_city_area') }}" enctype="multipart/form-data">
                                    {{csrf_field() }}
                                    @if(isset($area_details))
                                        <input type="hidden" name="id" value="{{$area_details->id}}">
                                    @endif
                                    <div class="row">
                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">{{ __('admin.forms.area_name') }}:<sup class="error">*</sup></label>
                                                <div class="col-sm-9">
                                                    <input type="text"  class="form-control" name="area_name" required id="area_name" placeholder="{{ __('admin.forms.area_name') }}" value="{{ (isset($area_details)) ? $area_details->name : old('name') }}">
                                                    <span class="error">{{ $errors->first('name') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">{{ __('admin.forms.status_colon') }}:<sup class="error">*</sup></label>
                                                <div class="col-sm-9">
                                                    <select name="status" id="status" class="form-control" required >
                                                        <option value="1" {{ (isset($area_details)) && $area_details->status == 1 ? "selected" : '' }} >On</option>
                                                        <option value="0" {{ (isset($area_details)) && $area_details->status == 0 ? "selected" : '' }} >Off</option>
                                                    </select>
                                                    <span class="error">{{ $errors->first('status') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="latitude" id="lat" value="{{ isset($area_details) ? $area_details->latitude : '' }}">
                                        <input type="hidden" name="longitude" id="lang" value="{{ isset($area_details) ? $area_details->longitude : '' }}">

                                        <div class="form-group col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <input id="searchInput" name="store_address" class="input-controls form-control"
                                                               value="{{ (isset($area_details)) ? $area_details->area_name : old('area_name')}}" type="text" placeholder="{{ __('admin.forms.enter_location') }}">
                                                        <div class="map" id="map" style="width: 100%; height: 500px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-12"></label>
                                        <div class="col-sm-10">
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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ isset($general_settings)? ($general_settings->map_key != Null)? $general_settings->map_key : 0 : 0 }}&callback=initMap&libraries=drawing,places&v=weekly" defer></script>
    <script>
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
    <script>
        function initMap() {
            var lati = '{{ isset($general_settings->address_lat) ? $general_settings->address_lat : 22.3039 }}';
            var longi = '{{ isset($general_settings->address_long) ? $general_settings->address_long : 70.8022 }}';
            @if(!isset($area_details))
                const map = new google.maps.Map(document.getElementById("map"), {
                        center: { lat: lati - 0, lng: longi - 0 },
                        zoom: 8,
                    });
                var input = $("#searchInput")[0];
                var geocoder = new google.maps.Geocoder();
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', map);
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }
                    let locality_types = ['locality','sublocality','postal_code','administrative_area_level_1','administrative_area_level_2','administrative_area_level_3'];
                    let locality_types_2 = ['country'];
                    const found = place.types.some(r=> locality_types.includes(r));
                    const found2 = place.types.some(r=> locality_types_2.includes(r));
                    if(found){
                        map.setCenter(place.geometry.location);
                        map.setZoom(8);
                    } else if(found2){
                        map.setCenter(place.geometry.location);
                        map.setZoom(4);
                    } else{
                        map.setCenter(place.geometry.location);
                        map.setZoom(18);
                    }
                });
                const drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    editable: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [
                            google.maps.drawing.OverlayType.POLYGON,
                        ],
                    },
                    markerOptions: {
                        icon:
                            "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
                    },
                    circleOptions: {
                        fillColor: "#ffff00",
                        fillOpacity: 1,
                        strokeWeight: 5,
                        clickable: false,
                        editable: true,
                        zIndex: 1,
                    },
                    polygonOptions: {
                        strokeColor: "#000",
                        strokeOpacity: 0.8,
                        strokeWeight: 5,
                        fillColor: "#8e8585",
                        fillOpacity: 0.35,

                    }
                });

                //for add
                drawingManager.setMap(map);
                google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
                    drawingManager.setDrawingMode(null);
                    if (event.type == 'polygon') {
                        var str_lat = '';
                        var str_lang = '';
                        $.each(event.overlay.getPath().getArray(), function (key, latlng) {
                            var lat = latlng.lat();
                            var lon = latlng.lng();
                            str_lat += lat + ',';
                            str_lang += lon + ',';
                        });
                        $("#lat").val(str_lat.substring(0, str_lat.length - 1));
                        $("#lang").val(str_lang.substring(0, str_lang.length - 1));
                    }
                });
            @endif


//code for edit
            @if(isset($area_details))
                var triangleCoords = [];
                var latitude = "{{ $area_details->latitude }}";
                var longitude = "{{ $area_details->longitude }}";
                var len1 = latitude.split(',').length;
                var len2 = longitude.split(',').length;
                for (var i=0,j=0; i<len1,j<len2; i++,j++) {
                    triangleCoords.push({
                        lat: parseFloat(latitude.split(',')[i]),
                        lng: parseFloat(longitude.split(',')[i]),
                    });
                }
                let lat = parseFloat(latitude.split(',')[0]);
                let lng = parseFloat(longitude.split(',')[0]);
                const edit_map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: lat, lng: lng },
                    zoom: 10,
                });
                var latlng = [
                    new google.maps.LatLng(lat, lng),
                    new google.maps.LatLng(latitude.split(',')[parseInt(3)], longitude.split(',')[parseInt(3)]),
                ];
                var latlngbounds = new google.maps.LatLngBounds();
                for (var i = 0; i < latlng.length; i++) {
                    latlngbounds.extend(latlng[i]);
                }
                edit_map.fitBounds(latlngbounds);
                var input = $("#searchInput")[0];
                var geocoder = new google.maps.Geocoder();
                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo('bounds', edit_map);
                autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        window.alert("Autocomplete's returned place contains no geometry");
                        return;
                    }
                    let locality_types = ['locality','sublocality','postal_code','administrative_area_level_1','administrative_area_level_2','administrative_area_level_3'];
                    let locality_types_2 = ['country'];
                    const found = place.types.some(r=> locality_types.includes(r));
                    const found2 = place.types.some(r=> locality_types_2.includes(r));
                    if(found){
                        edit_map.setCenter(place.geometry.location);
                        edit_map.setZoom(8);
                    } else if(found2){
                        edit_map.setCenter(place.geometry.location);
                        edit_map.setZoom(4);
                    } else{
                        edit_map.setCenter(place.geometry.location);
                        edit_map.setZoom(18);
                    }
                });
               // triangleCoords.pop();
                // Construct the polygon.
                drawingManager1 = new google.maps.Polygon({
                    paths: triangleCoords,
                    strokeColor: "#000",
                    strokeOpacity: 0.8,
                    strokeWeight: 5,
                    fillColor: "#8e8585",
                    fillOpacity: 0.35,
                    editable: true,
                    draggable: false
                });
                drawingManager1.setMap(edit_map);

                google.maps.event.addListener(drawingManager1, 'set_at', function (event) {
                    if (event.type == 'polygon') {
                        var str_input = '';
                        var str_lat = '';
                        var str_lang = '';
                        $.each(event.overlay.getPath().getArray(), function (key, latlng) {
                            var lat = latlng.lat();
                            var lon = latlng.lng();
                            str_input += lat + ' ' + lon + ',';
                            str_lat += lat + ',';
                            str_lang += lon + ',';
                        });
                        $("#lat").val(str_lat.substring(0, str_lat.length - 1));
                        $("#lang").val(str_lang.substring(0, str_lang.length - 1));
                    }
                });
                drawingManager1.getPaths().forEach(function (path, index) {
                    google.maps.event.addListener(path, 'insert_at', function (event) {
                        // New point
                        var str_input = '';
                        var str_lat = '';
                        var str_lang = '';
                        $.each(path.getArray(), function (key, latlng) {
                            var lat = latlng.lat();
                            var lon = latlng.lng();
                            str_input += lat + ' ' + lon + ',';
                            str_lat += lat + ',';
                            str_lang += lon + ',';
                        });
                        $("#lat").val(str_lat.substring(0, str_lat.length - 1));
                        $("#lang").val(str_lang.substring(0, str_lang.length - 1));
                    });

                    google.maps.event.addListener(path, 'remove_at', function (event) {
                        // Point was removed
                        var str_input = '';
                        var str_lat = '';
                        var str_lang = '';
                        $.each(path.getArray(), function (key, latlng) {
                            var lat = latlng.lat();
                            var lon = latlng.lng();
                            str_input += lat + ' ' + lon + ',';
                            str_lat += lat + ',';
                            str_lang += lon + ',';
                        });
                        $("#lat").val(str_lat.substring(0, str_lat.length - 1));
                        $("#lang").val(str_lang.substring(0, str_lang.length - 1));
                    });

                    google.maps.event.addListener(path, 'set_at', function (event) {
                        // Point was moved
                        var str_input = '';
                        var str_lat = '';
                        var str_lang = '';
                        $.each(path.getArray(), function (key, latlng) {
                            var lat = latlng.lat();
                            var lon = latlng.lng();
                            str_input += lat + ' ' + lon + ',';
                            str_lat += lat + ',';
                            str_lang += lon + ',';
                        });
                        $("#lat").val(str_lat.substring(0, str_lat.length - 1));
                        $("#lang").val(str_lang.substring(0, str_lang.length - 1));
                    });
                });
            @endif
        }

    </script>
@endsection
