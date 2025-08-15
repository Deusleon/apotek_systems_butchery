<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
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
        @if($pharmacy['logo'])
            <div class="logo-container">
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            </div>
        @endif
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h3 align="center">{{$pharmacy['name']}}</h3>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>
    <h3 align="center">RECEIPT</h3>
    @foreach($data as $datas => $dat)
        <div class="full-row" style="padding-top: 2%">
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Sold By: </b></div>
                    <div class="col-50" align="right">{{$dat[0]['sold_by']}}</div>
                </div>
            </div>
            <div class="col-50"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>TIN #: </b></div>
                    <div class="col-50" align="right">{{$pharmacy['tin_number']}}</div>
                </div>
            </div>
        </div>
        <div class="full-row">
            <div class="col-30">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Sales Date:</b></div>
                    <div class="col-50" align="right">{{date('j M, Y', strtotime($dat[0]['created_at']))}}</div>
                </div>
            </div>
            <div class="col-50"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Recept #: </b></div>
                    <div class="col-50" align="right">{{$datas}}</div>
                </div>
            </div>
        </div>
</div>

<div class="row" style="margin-top: 13%">
    <table class="table table-sm" id="table-detail" align="center">
        <tr>
            <th align="left">SN</th>
            <th align="left">Product Name</th>
            <th align="right">Quantity</th>
            <th align="right">Price</th>
            <th align="right">Sub Total</th>
            <th align="right">Discount</th>
            <th align="right">VAT</th>
            <th align="right">Amount</th>
        </tr>

        @foreach($dat as $item)
            <tr>
                <td align="left">{{$item['sn']}}</td>
                <td align="left">{{$item['name']}}</td>
                <td align="right">{{$item['quantity']}}</td>
                <td align="right">{{number_format($item['price']/$item['quantity'],2)}}</td>
                <td align="right">{{number_format($item['sub_total'],2)}}</td>
                <td align="right">{{number_format($item['discount'],2)}}</td>
                <td align="right">{{number_format($item['vat'],2)}}</td>
                <td align="right">{{number_format($item['amount'],2)}}</td>
            </tr>
        @endforeach
        @endforeach
    </table>
</div>
<div class="full-row" style="padding-top: 1%">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Sub Total: </b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['grand_total']-$dat[0]['total_vat']),2)}}</div>
        </div>
    </div>
</div>
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Discount: </b></div>
            <div class="col-50" align="right">{{number_format($dat[0]['total_discount'],2)}}</div>
        </div>
    </div>
</div>
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
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
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Paid:</b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['paid']),2)}}</div>
        </div>
    </div>
</div>
<div class="full-row">
    <div class="col-25"></div>
    <div class="col-50"></div>
    <div class="col-25">
        <div class="full-row">
            <div class="col-50" align="left"><b>Balance:</b></div>
            <div class="col-50" align="right">{{number_format(($dat[0]['balance']),2)}}</div>
        </div>
    </div>
</div>
</body>
</html>

