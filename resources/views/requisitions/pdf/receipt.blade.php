<!DOCTYPE html>
<html>
<head>
    <title>Requisition</title>
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
    <h3 align="center">REQUISITION</h3>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['name']}}</h3>
    <h5 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h5>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h6>
    <h5 align="center" style="margin-top: -2%">TIN: {{$pharmacy['tin_number']}}</h5>
    <h5 align="center" style="margin-top: -2%">VRN: {{$pharmacy['vrn_number']}}</h5>
    <h5 align="center" style="margin-top: -2%">Requisition #: {{$requisition->req_no}}</h5>
    <h5 align="center" style="margin-top: -2%">Date:  {{date('j M, Y', strtotime($requisition->created_at))}}</h5>
</div>

<div class="row" style="margin-top: 13%">
    <table class="table table-sm" id="table-detail" align="center">
        <tr>
            <th align="left">Product</th>
            <th align="right">Unit</th>
            <th align="right">Qty</th>
        </tr>

        @foreach($requisitionDet as $item)
            <tr>
                <td align="left">{{$item->products_->name ?? ''}}</td>
                <td align="right">{{$item->unit ?? '--'}}</td>
                <td align="right">{{ number_format($item->quantity,0) ?? ''}}</td>
            </tr>
        @endforeach

    </table>
</div>

<h6 align="center">Created By: {{$requisition->creator->name}}</h6>
<h6 align="center" style="font-style: italic; margin-top: -2%">{{$pharmacy['slogan']}}</h6>

</body>
</html>

