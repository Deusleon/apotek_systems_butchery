<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>
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
        <div class="logo-container">
            <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
        </div>
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h3 align="center">{{$pharmacy['name']}}</h3>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>
    <h3 align="center">Purchase Order</h3>
</div>

<div class="row" style="margin-top: 13%">
    <table class="table table-sm" id="table-detail" align="center">
        <tr>
            <th align="left">Product Name</th>
            <th align="right">Quantity</th>
            <th align="right">Price</th>
            <th align="right">Sub Total</th>
            <th align="right">VAT</th>
            <th align="right">Amount</th>
        </tr>

        @foreach($data as $item)
            <tr>
                <td align="left">{{$item->product['name']}}</td>
                <td align="right">
                    <div style="margin-right: 50%">{{$item->ordered_qty}}</div>
                </td>
                <td align="right">{{number_format($item->unit_price,2)}}</td>
                <td align="right">{{number_format($item->sub_total,2)}}</td>
                <td align="right">{{number_format($item->vat,2)}}</td>
                <td align="right">{{number_format($item->amount,2)}}</td>
            </tr>
        @endforeach
    </table>
</div>
<div class="full-row" style="padding-top: 1%">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row" style="background-color: #f2f2f2;">
            <div class="col-50" align="left"><b>Sub Total: </b></div>
            <div class="col-50" align="right">{{number_format($data->max('sub_totals'),2)}}</div>
        </div>
    </div>
</div>
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>VAT: </b></div>
            <div class="col-50" align="right">{{number_format($data->max('vats'),2)}}</div>
        </div>
    </div>
</div>
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row" style="background-color: #f2f2f2;">
            <div class="col-50" align="left"><b>Total:</b></div>
            <div class="col-50" align="right">{{number_format($data->max('total'),2)}}</div>
        </div>
    </div>
</div>

</body>
</html>

