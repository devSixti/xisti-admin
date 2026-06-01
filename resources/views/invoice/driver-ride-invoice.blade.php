<!DOCTYPE html>
{{--<html>
<head>
    <title>{{ $title }}</title>
    <meta charset="UTF-8">
</head>
<body>
<h1>{{ $title }}</h1>
<p>This PDF document is generated using domPDF in Laravel.</p>
</body>
</html>--}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ __('driver_messages.355',[],$user_language) }} {{ ($ride_no !="")?"#".$ride_no:"--" }}</title>

    <style>
        html,
        body {
            margin: 10px;
            padding: 10px;
            font-family: sans-serif;
        }
        h1,h2,h3,h4,h5,h6,p,span,label {
            font-family: sans-serif;
        }
        table {
            width: 100%;

            border-collapse: collapse;
            margin-bottom: 0px !important;
        }
        table thead th {
            height: 28px;
            text-align: left;
            font-size: 16px;
            font-family: sans-serif;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .heading {
            font-size: 24px;
            margin-top: 12px;
            margin-bottom: 12px;

            font-family: sans-serif;
        }
        .small-heading {
            font-size: 18px;
            font-family: sans-serif;
        }
        .total-heading {
            font-size: 18px;
            font-weight: 700;
            font-family: sans-serif;
        }
        .order-details tbody tr td:nth-child(1) {
            width: 20%;
        }
        .order-details tbody tr td:nth-child(3) {
            width: 20%;
        }

        .text-start {

            text-align: left;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .company-data span {
            margin-bottom: 4px;
            display: inline-block;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 400;
        }
        .no-border {
            border: 1px solid #fff !important;
            border-bottom: 1px solid #ddd !important;
        }

        .bg-blue {

            background-color: #9ca3af;
            color: #fff;
        }
    </style>
</head>
<body>

<table class="order-details">
    <thead>
    <tr>
        <th width="50%" colspan="2">
            <img src="{{$web_site_logo}}" width="250" height="60" alt="logo" border="0" />
        </th>
        <th width="50%" colspan="2" class="text-end company-data">
            <span>{{__('driver_messages.344',[],$user_language)}} :{{ ($ride_no !="")?"#".$ride_no:"--" }}</span> <br>
            <span>{{__('driver_messages.345',[],$user_language)}} :{{ ($order_date !="")?$order_date:"--" }}</span> <br>
            {{--<span>{{__('driver_messages.232',[],$user_language)}} :{{ ($delivery_address !="")?trim($delivery_address):"--" }}</span> <br>--}}
        </th>

    </tr>
    <tr class="bg-blue">
        <th width="50%" colspan="2">{{__('driver_messages.346',[],$user_language)}}</th>
        <th width="50%" colspan="2">{{__('driver_messages.347',[],$user_language)}}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td rowspan="2">{{__('driver_messages.366',[],$user_language)}}:</td>
        <td rowspan="2">{{  ($ride_no !="")?"#".$ride_no:"--" }}</td>

        <td>{{__('driver_messages.348',[],$user_language)}}:</td>
        <td>{{ ($user_name !="")?$user_name:"--" }}</td>
    </tr>
    <tr>
        <td>{{__('driver_messages.349',[],$user_language)}}:</td>
        <td>{{  ($email !="")?$email:"--"  }}</td>
    </tr>
    <tr>
        <td>{{__('driver_messages.350',[],$user_language)}}:</td>
        <td>{{ ($driver_name !="")?$driver_name:"--" }}</td>

        <td>{{__('driver_messages.351',[],$user_language)}}:</td>
        <td>{{ ($payment_type !="")?$payment_type:"--" }}</td>
    </tr>
    <tr>
        <td>{{__('driver_messages.352',[],$user_language)}}:</td>
        <td colspan="3">{{ ($pickup_address !="")?$pickup_address:"--" }}</td>
    </tr>
    <tr>
        <td>{{__('driver_messages.353',[],$user_language)}}:</td>
        <td>{{ ($order_status !="")?$order_status:"--" }}</td>

        <td>{{__('driver_messages.354',[],$user_language)}}:</td>
        <td>{{ ($order_date !="")?$order_date:"--" }}</td>
    </tr>
    <tr>
        <td >{{__('driver_messages.360',[],$user_language)}}:</td>
        <td colspan="3">{{ ($pickup_address !="")?$pickup_address:"--" }}</td>
    </tr>
    <tr>
        <td >{{__('driver_messages.361',[],$user_language)}}:</td>
        <td colspan="3">{{ ($destination_address !="")?$destination_address:"--" }}</td>
    </tr>
    </tbody>
</table>

<table>
    <thead>
    <tr>
        <th class="no-border text-start heading" colspan="5">
            {{__('driver_messages.362',[],$user_language)}}
        </th>
    </tr>
    </thead>
    <tbody>

    @if(isset($trip_value) && $trip_value != "0")
        <tr>
            <td colspan="4" class="total-heading"><small>{{__('driver_messages.364',[],$user_language)}} (viaje)</small>:</td>
            <td colspan="1" class="total-heading"><small>{{ $trip_value }}</small></td>
        </tr>
    @endif
    @if(isset($commission_amount) && $commission_amount != "0")
        <tr>
            <td colspan="4" class="total-heading"><small>Comisión plataforma</small>:</td>
            <td colspan="1" class="total-heading"><small>{{ $commission_amount }}</small></td>
        </tr>
    @endif
    @if(isset($vat_on_commission) && $vat_on_commission != "0")
        <tr>
            <td colspan="4" class="total-heading"><small>IVA sobre comisión</small>:</td>
            <td colspan="1" class="total-heading"><small>{{ $vat_on_commission }}</small></td>
        </tr>
    @endif
    @if(isset($total_deduction) && $total_deduction != "0")
        <tr>
            <td colspan="4" class="total-heading"><small>Total descuentos</small>:</td>
            <td colspan="1" class="total-heading"><small>{{ $total_deduction }}</small></td>
        </tr>
    @endif
    @if($total_toll_charge != "0")
        <tr>
            <td colspan="4" class="total-heading"><small>{{__('driver_messages.363',[],$user_language)}}</small>:
            </td>
            <td colspan="1" class="total-heading"><small>{{ $total_toll_charge }}</small></td>
        </tr>
    @endif
    </tbody>
</table>

<br>
<p class="text-center">
    {{__('driver_messages.365',[],$user_language)}}
</p>

</body>
</html>

