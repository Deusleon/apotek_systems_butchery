<!DOCTYPE html>
<html>
<head>
    <title>Cash Sale Details Report</title>
    <style>

        body {
            /*font-size: 30px;*/
        }

        table, th, td {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            padding: 10px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            /*border-spacing: 5px;*/
            width: 100%;
            /*margin-top: 2%;*/
        }

        #table-detail-1 {
            width: 100%;
            margin-top: 3%;
        }

        #table-detail-2 {
            width: 100%;
            margin-top: 0%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr > {
            /*line-height: 13px;*/
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
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
            @if($pharmacy['logo'])
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            @endif
        </div>
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h4 align="center">{{$pharmacy['name']}}</h4>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
    <h2 align="center" style="margin-top: -2%">Cash Sales Details Report</h2>
    <h5 align="center" style="margin-top: -2%">Phone: {{$pharmacy['phone']}}</h5>
    <h4 align="center" style="margin-top: -2%">{{$pharmacy['date_range']}}</h4>
    @foreach($data[0][0] as $key => $dat)
        {{--        {{$pharmacy['tin_number']}} {{date('j M, Y', strtotime($dat[0]['created_at']))}}--}}
        <table id="table-detail-main">
            <tr>
                <td><b>Date:</b> {{date('j M, Y', strtotime($dat[0]['created_at']))}}</td>
            </tr>
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white;">
                <th align="left">SN</th>
                <th align="left">Product Name</th>
                <th align="right">Quantity</th>
                <th align="right">Price</th>
                {{--                <th align="right">Sub Total</th>--}}
                {{--                <th align="right">Discount</th>--}}
                <th align="right">VAT</th>
                <th align="right">Amount</th>
            </tr>
            </thead>
            @foreach($dat as $item)
                <tr>
                    <td align="left">{{$loop->iteration}}</td>
                    <td align="left">{{$item['name']}}</td>
                    <td align="right">{{$item['quantity']}}</td>
                    <td align="right">{{number_format($item['price']/$item['quantity'],2)}}</td>
                    {{--                    <td align="right">{{number_format($item['sub_total'],2)}}</td>--}}
                    {{--                    <td align="right">{{number_format($item['discount'],2)}}</td>--}}
                    <td align="right">{{number_format($item['vat'],2)}}</td>
                    <td align="right">{{number_format($item['amount'],2)}}</td>
                </tr>
            @endforeach
        </table>

        @foreach($data[0][2][$key] as $e)
            {{--            {{number_format($e['test_total'],2)}}--}}
            <table style="width: 103%; background-color: white">
                <thead>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </thead>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" style="padding-top: -3%"><b>Sub Total:</b></td>
                    <td align="right"
                        style="padding-top: -3%">{{number_format($e['amount_total']-$e['vat_total']+$e['discount_total'],2)}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" style="padding-top: -3%"><b>Discount:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($e['discount_total'],2)}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" style="padding-top: -3%"><b>VAT:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($e['vat_total'],2)}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right" style="padding-top: -3%"><b>Total:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($e['amount_total'],2)}}</td>
                </tr>
            </table>
        @endforeach



        <hr>
    @endforeach

</div>

</body>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 280;
        $y = 820;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }


</script>

</html>

