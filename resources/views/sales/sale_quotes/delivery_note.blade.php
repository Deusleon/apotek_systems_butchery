<!DOCTYPE html>
<html>
<head>
    <title>Delivery Note</title>
    <style>

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
            font-size: x-small;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 13px;
            width: 50%;
        }

        .col-100 {
            display: inline-block;
            font-size: 13px;
            width: 90%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }


        #table-detail {
            border-spacing: 6%;
            width: 96%;
            margin-top: -13%;
            border: none;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 160px;
            max-height: 160px;
        }


    </style>
</head>
<body>
<div class="row">
    <div id="container">
        @if($pharmacy['logo'])
            <div class="logo-container">
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            </div>
        @endif
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h3 align="center">DELIVERY NOTE</h3>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['name']}}</h3>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h6>

    @foreach($data as $datas => $dat)

        <!-- Container for the first row -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin-top: 10px;">
            <div style="flex: 1; padding-left: 3%;text-align: left;">
                <b>Customer TIN:</b>{{$data[$datas][0]['customer_tin'] ?? ' N/A'}}
            </div>
            <div style="text-align: right;">
                <b>Delivery Note #:</b> {{$datas}}
            </div>
        </div>

        <!-- Container for the customer information -->
        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin-top: 5px;">
            <div style="flex: 1; padding-left: 3%;text-align: left;">

                @if($dat[0]['customer'])
                    @if($dat[0]['customer'])
                        @if($dat[0]['customer']!="CASH")
                            <b>Customer Name: </b> {{$dat[0]['customer']}}
                        @endif
                    @endif
                @endif
            </div>
            <div style="text-align: right;">
                <b>Date:</b> {{date('j M, Y', strtotime($dat[0]['created_at']))}}
            </div>
        </div>


</div>

<div class="row" style="margin-top: 13%">
    <table class="table table-sm" id="table-detail" align="center">
        <tr>
            <th align="left">SN</th>
            <th align="left">Description</th>
            <th align="right">Quantity</th>
        </tr>

        @foreach($dat as $item)
            <tr>
                <td align="left">{{$item['sn']}}</td>
                <td align="left">{{$item['name']}}</td>
                <td align="right">{{$item['quantity']}}</td>
            </tr>
        @endforeach
        @endforeach
    </table>
</div>

<h6 align="center">Prepared By: {{$dat[0]['sold_by']}}</h6>
<h6 align="center" style="font-style: italic; margin-top: -2%">{{$pharmacy['slogan']}}</h6>

</body>
</html>
