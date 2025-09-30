<!DOCTYPE html>
<html>
<head>
    <title>Material Received Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
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
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: -6%;
            border-collapse: collapse;
        }

        #table-detail tr > {
            line-height: 13px;
        }

        tr:nth-child(even) {
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

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 35%;
        }

        .col-15 {
            display: inline-block;
            font-size: 13px;
            width: 15%;
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
    <h1 align="center">{{$pharmacy['name']}}</h1>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'].' | '.$pharmacy['website']}}</h3>
    <h2 align="center" style="margin-top: -1%">Material Received Report</h2>
    <h4 align="center" style="margin-top: -1%">From: {{date('Y-m-d',strtotime($data->first()->dates[0]))}} To: {{date('Y-m-d',strtotime($data->first()->dates[1]))}}</h4>

    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">

            <table id="table-detail-main">
                <tr>
                    <td>Supplier: {{$data->first()->supplier_name}}</td>
                    @if(!(empty($data->first()->invoice_nos)))
                        <td>Invoice: {{$data->first()->invoice_nos}}</td>
                    @endif
                </tr>
            </table>

            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th align="left">Product Name</th>
                    <th align="center">Quantity</th>
                    <th align="right">Buy Price</th>
                    <th align="right">Sell Price</th>
                    <th align="right">Profit</th>
                    <th align="left">Receive Date</th>
                    <th align="left">Received By</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td align="left">
                            {{$item->product['name'].' '.$item->product['brand'].' '.$item->product['pack_size'].$item->product['sales_uom']}}
                        </td>
                        <td align="center">{{number_format($item->quantity,0)}}</td>
                        <td align="right">{{number_format($item->unit_cost,2)}}</td>
                        <td align="right">{{number_format($item->sell_price,2)}}</td>
                        <td align="right">{{number_format($item->item_profit,2)}}</td>
                        <td align="left">{{date('Y-m-d',strtotime($item->created_at))}}</td>
                        <td align="left">{{$item->user['name']}}</td>
                    </tr>
                @endforeach
            </table>
            <hr>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-35"><div class="full-row"></div></div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-25">
                    <div class="full-row">
                        <div class="col-50" align="left"><b>Total Buy: </b></div>
                        <div class="col-50" align="right">{{number_format($data->first()->total_bp,2)}}</div>
                    </div>
                </div>
            </div>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-35"><div class="full-row"></div></div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-25">
                    <div class="full-row">
                        <div class="col-50" align="left"><b>Total Sales: </b></div>
                        <div class="col-50" align="right">{{number_format($data->first()->total_sp,2)}}</div>
                    </div>
                </div>
            </div>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-35"><div class="full-row"></div></div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-25">
                    <div class="full-row">
                        <div class="col-50" align="left"><b>Total Profit: </b></div>
                        <div class="col-50" align="right">{{number_format($data->first()->total_p,2)}}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 400;
        $y = 560;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
     }
</script>

</body>
</html>
