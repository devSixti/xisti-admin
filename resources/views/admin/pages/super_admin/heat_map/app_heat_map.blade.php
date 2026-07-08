@extends('admin.layout.common_layout')
@section('title')
    {{ __('admin.heat_map.transport_heat_map') }}
@endsection
@section('page-css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('/assets/css/widget/widget.css') }}">
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

        #searchInput {
            z-index: 9999;
            position: relative;
            left: 230px;
            top: 40px;
            width: 24%;
        }

        #map-canvas {
            position: relative;
            width: 300px;
            height: 500px;
        }

        #mapview {
            /*position: initial;*/
            /*top: 0;*/
            /*right: 0;*/
            /*bottom: 0;*/
            /*left: 0;*/
            /*width: 350px;*/
            /*height: 95%;*/
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            position: fixed !important;
        }
        .gm-style-mtc,.gm-svpc{
            display: none;
        }

        #floating-panel {
            position: absolute;
            top: 10px;
            left: 15%;
            z-index: 5;
            background-color: #fff;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            text-align: center;
            font-family: "Roboto", "sans-serif";
        }

        @media screen and (max-width: 768px) {
            #floating-panel {
                left: 8px;
                right: 8px;
                top: 8px;
            }

            #filter_key {
                width: 100%;
            }
        }
    </style>
@endsection
@section('page-content')

    <div class="admin-app-heat-map">
        <div class="">
            <div id="floating-panel" class="admin-map-toolbar">
                <select name="filter_key" id="filter_key" class="form-control form-control-sm">
                    <option value="1" {{ ($filter_key == "1")?"selected":""  }}>{{ __(key : "user_messages.one_day", locale: $userLanguage) }}</option>
                    <option value="2" {{ ($filter_key == "2")?"selected":""  }}>{{ __(key : "user_messages.seven_days", locale: $userLanguage) }}</option>
                    <option value="3" {{ ($filter_key == "3")?"selected":""  }}>{{ __(key : "user_messages.last30_days", locale: $userLanguage) }}</option>
                </select>
            </div>
            <div id="mapview"></div>
        </div>
    </div>

@endsection
@section('page-js')
    <script>
        let map, heatmap;
        var map_lat_data = '{{ ($driver_lat != "")?$driver_lat: (isset($general_settings)? ($general_settings->address_lat != Null)? $general_settings->address_lat : 22.308155 : 22.308155) }}' - 0;
        var map_long_data = '{{ ($driver_long!= "")?$driver_long:(isset($general_settings)? ($general_settings->address_long != Null)? $general_settings->address_long : 70.800705 : 70.800705) }}' - 0;
        async function initMap() {
            const { Map } = await google.maps.importLibrary("maps");
            const { HeatmapLayer } = await google.maps.importLibrary("visualization");
            map = new Map(document.getElementById("mapview"), {
                zoom: 13,
                center: {lat: map_lat_data, lng: map_long_data},
                mapTypeId: "roadmap",
            });
            heatmap = new HeatmapLayer({
                data: getPoints(),
                map: map,
            });
            console.log("heatmap1");
            console.log(heatmap);
            //heatmap.set("gradient", heatmap.get("gradient") ? null : gradient);
            heatmap.set("radius", heatmap.get("radius") ? null : 12.5);
            // document.getElementById("toggle-heatmap").addEventListener("click", toggleHeatmap);
            // document.getElementById("change-gradient").addEventListener("click", changeGradient);
            // document.getElementById("change-opacity").addEventListener("click", changeOpacity);
            // document.getElementById("change-radius").addEventListener("click", changeRadius);
        }

        function toggleHeatmap() {
            heatmap.setMap(heatmap.getMap() ? null : map);
        }

        function changeGradient() {
            const gradient = [
                "rgba(0, 255, 255, 0)",
                "rgba(0, 255, 255, 1)",
                "rgba(0, 191, 255, 1)",
                "rgba(0, 127, 255, 1)",
                "rgba(0, 63, 255, 1)",
                "rgba(0, 0, 255, 1)",
                "rgba(0, 0, 223, 1)",
                "rgba(0, 0, 191, 1)",
                "rgba(0, 0, 159, 1)",
                "rgba(0, 0, 127, 1)",
                "rgba(63, 0, 91, 1)",
                "rgba(127, 0, 63, 1)",
                "rgba(191, 0, 31, 1)",
                "rgba(255, 0, 0, 1)",
            ];

            heatmap.set("gradient", heatmap.get("gradient") ? null : gradient);
        }

        function changeRadius() {
            heatmap.set("radius", heatmap.get("radius") ? null : 20);
        }

        function changeOpacity() {
            heatmap.set("opacity", heatmap.get("opacity") ? null : 0.2);
        }

        // Heatmap data: 500 Points
        function getPoints() {
            // new google.maps.LatLng(37.782551, -122.445368),
            @if(isset($latlong_array) && !empty($latlong_array))
                var a =  [
                    @foreach($latlong_array as $latlong)
                    { location: new google.maps.LatLng({{ $latlong[0]['pickup_latlong'] }}), weight: {{ $latlong[1] }} },
                    @endforeach
                ];
                return a;
            @endif
        }

        //function getPoints() {
        //    return [
        //        new google.maps.LatLng(37.751266, -122.403355),
        //    ];
        //}

        window.initMap = initMap;

        $(document).ready(function (){
            $("#filter_key").on("change",function (){
                var filter_key = $(this).val();
                ajaxCall(filter_key)

            });

             function  ajaxCall(filter_key=1){
                $.ajax({
                    type:'POST',
                    async :false,
                    cache: false,

                    /*contentType: "application/json",*/
                    dataType: "json",
                    /*processData: false,*/
                    url:'{{ route('post:heat.map') }}',
                    data:  {'filter_key':filter_key,"_token": "{{ csrf_token() }}"} ,
                    success:function(data){

                        $("#cover-spin").css('display',"block");
                        $('#cover-spin').height($( ".rightPartSection" ).height());
                        console.log(data);
                        if(data.success == true){

                            heatmap.setMap(null);
                            var arr_lat = [];
                            if(data.latlong_array.length > 0)
                            {
                                $.each( data.latlong_array, function( key, value ) {
                                    var values = value[0]['pickup_latlong'].split(",");
                                    var v1 = parseFloat(values[0])
                                    var v2 = parseFloat(values[1])
                                    arr_lat.push({location: new google.maps.LatLng(v1, v2), weight: value[1]})
                                });
                            }
                            heatmap = new google.maps.visualization.HeatmapLayer({
                                data: arr_lat,
                                map: map,
                            });
                        }else{
                            $("#cover-spin").css('display',"none");
                        }
                    }
                });
            }
        })
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ isset($general_settings)? ($general_settings->map_key != Null)? $general_settings->map_key : 0 : 0 }}&callback=initMap&v=weekly" defer></script>
@endsection
