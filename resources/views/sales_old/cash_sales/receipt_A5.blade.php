<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>

        /* Set A6 page size for printing */
        @page {
            size: 105mm 149mm;
            margin: 5mm; /* Smaller margins for half A5 */
        }

        body {
            font-family: Verdana, Arial, sans-serif;
            font-size: 10px; /* Reduced font size */
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px; /* Reduced padding for A6 */
            font-size: xx-small; /* Reduced table font size */
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .full-row {
            width: 100%;
            padding-left: 2%; /* Adjusted padding for A6 */
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 9px; /* Reduced font size */
            width: 50%;
        }

        .col-100 {
            display: inline-block;
            font-size: 9px; /* Reduced font size */
            width: 90%;
        }

        .col-25 {
            display: inline-block;
            font-size: 9px; /* Reduced font size */
            width: 25%;
        }

        #table-detail {
            width: 96%;
            margin-top: -3%; /* Adjusted for A6 size */
            border: none;
        }

        #container .logo-container {
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 80px; /* Further reduced image size */
            max-height: 80px;
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
    <h4 align="center">RECEIPT</h4> <!-- Reduced heading size -->
    <h4 align="center" style="margin-top: -2%">{{$pharmacy['name']}}</h4>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h6>
    <h6 align="center" style="margin-top: -2%">TIN: {{$pharmacy['tin_number']}}</h6>
    <h6 align="center" style="margin-top: -2%">VRN: {{$pharmacy['vrn_number']}}</h6>

    @foreach($data as $datas => $dat)

        <div style="margin-left: 5%; font-size: 9px; width: 25%; margin-top: 5%; padding: -1.6%"><b>TIN:</b>
        </div>
        <div align="left"
             style="margin-left: 12%; font-size: 9px; width: 25%; margin-top: -1%; padding-top: -1.6%; padding-left: 1%">
            {{$data[$datas][0]['customer_tin']}}
        </div>
        <div style="margin-left: 75%; font-size: 9px; width: 25%; margin-top: -10%; padding: -1.6%"><b>Receipt #:</b>
        </div>
        <div align="left"
             style="margin-left: 85%; font-size: 9px; width: 25%; margin-top: -10%; padding-top: -1.6%; padding-left: 1%">
            {{$datas}}
        </div>

        <div style="margin-left: 5%; font-size: 9px; width: 25%;margin-top: 2%; padding: -1.6%"><b>Customer:</b>
        </div>
        @if($dat[0]['customer'])
            <div align="left"
                 style="margin-left: 12%; font-size: 9px; width: 80%; margin-top: -10%; padding-top: -1.6%; padding-left: 1%">
                {{$dat[0]['customer']}}
            </div>
        @else
            <div align="left"
                 style="margin-left: 12%; font-size: 9px; width: 25%; margin-top: -10%; padding-top: -1.6%; padding-left: 1%">
                CASH
            </div>
        @endif

        <div style="margin-left: 75%; font-size: 9px; width: 25%; margin-top: -10%; padding: -1.6%"><b>Sales Date:</b>
        </div>
        <div align="left"
             style="margin-left: 85%; font-size: 9px; width: 25%; margin-top: -10%; padding-top: -1.6%; padding-left: 1%">
            {{date('j M, Y', strtotime($dat[0]['created_at']))}}
        </div>

</div>

<div class="row" style="margin-top: 13%">
    <table class="table table-sm" id="table-detail" align="center">
        <tr>
            <th align="left">SN</th>
            <th align="left">Description</th>
            <th align="right">Quantity</th>
            <th align="right">Price</th>
            <th align="right">VAT</th>
            <th align="right">Amount</th>
        </tr>

        @foreach($dat as $item)
            <tr>
                <td align="left">{{$item['sn']}}</td>
                <td align="left">{{$item['name']}}</td>
                <td align="right">{{$item['quantity']}}</td>
                <td align="right">{{number_format($item['price']/$item['quantity'],2)}}</td>
                <td align="right">{{number_format($item['vat'],2)}}</td>
                <td align="right">{{number_format($item['amount'],2)}}</td>
            </tr>
        @endforeach
        @endforeach
    </table>
</div>

<!-- Sub Total, Discount, VAT, and Total Summary -->
<div class="full-row" style="padding-top: 1%">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row" style="background-color: #f2f2f2;">
            <div class="col-50" align="left"><b>Sub Total: </b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['grand_total']-$dat[0]['total_vat'] + $dat[0]['discount_total']),2)}}</div>
        </div>
    </div>
</div>

<div class="full-row" style="padding-top: 0%">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Discount: </b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['discount_total']),2)}}</div>
        </div>
    </div>
</div>

<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row" style="background-color: #f2f2f2;">
            <div class="col-50" align="left"><b>VAT: </b></div>
            <div class="col-50" align="right">{{number_format($dat[0]['total_vat'],2)}}</div>
        </div>
    </div>
</div>

<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Total:</b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['grand_total']),2)}}</div>
        </div>
    </div>
</div>

<!-- Payment details if available -->
@if($page == -1)
    <hr>
    <div class="full-row" style="padding-top: 1%">
        <div class="col-25"></div>
        <div class="col-50"></div>
        <div class="col-25">
            <div class="full-row" style="background-color: #f2f2f2;">
                <div class="col-50" align="left"><b>Paid: </b></div>
                <div class="col-50" align="right">{{number_format($dat[0]['paid'],2)}}</div>
            </div>
        </div>
    </div>
    <div class="full-row" style="padding-top: 0%">
        <div class="col-25"></div>
        <div class="col-50"></div>
        <div class="col-25">
            <div class="full-row">
                <div class="col-50" align="left"><b>Balance: </b></div>
                <div class="col-50" align="right">{{number_format($dat[0]['grand_total'] - $dat[0]['paid'],2)}}</div>
            </div>
        </div>
    </div>
    <hr>
    <p><b>Remarks:</b> {{$dat[0]['remark']}}</p>
@endif

<!-- Footer -->
<h6 align="center">Sold By: {{$dat[0]['sold_by']}}</h6>
<h6 align="center" style="font-style: italic; margin-top: -2%">{{$pharmacy['slogan']}}</h6>

</body>
</html>
