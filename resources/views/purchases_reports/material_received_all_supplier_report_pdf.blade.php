<!DOCTYPE html>
<html>
<head>
    <title>Material Received Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th {
            border-collapse: collapse;
            padding: 8px;
        }

        table,
        td {
            border-collapse: collapse;
            padding: 5px;
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
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
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

    <div class="row">
        <div class="col-md-12">
            @php
                $grand_total_cost = 0;
                $grand_total_sell = 0;
                $grand_total_profit = 0;
            @endphp

            @foreach($data[0]['data'] as $key => $items)
                <table id="table-detail-main">
                    <tr>
                        <td><b>Supplier: </b>{{$key}}</td>
                    </tr>
                </table>

                <table id="table-detail" align="center">
                    <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="left" style="width: 1%;">#</th>
                        <th align="left">Product Name</th>
                        <th align="center">Quantity</th>
                        <th align="right">Buy Price</th>
                        <th align="right">Sell Price</th>
                        <th align="right">Profit</th>
                        <th align="left">Receive Date</th>
                        <th align="left">Received By</th>
                    </tr>
                    </thead>

                    @foreach($items as $item)
                        <tr>
                            <td align="left">{{$loop->iteration}}.</td>
                            <td align="left">{{$item['product_name']}}</td>
                            <td align="center">{{number_format($item['quantity'],0)}}</td>
                            <td align="right">{{number_format($item['unit_cost'],2)}}</td>
                            <td align="right">{{number_format($item['sell_price'],2)}}</td>
                            <td align="right">{{number_format($item['profit'],2)}}</td>
                            <td align="left">{{date('Y-m-d',strtotime($item['date']))}}</td>
                            <td align="left">{{$item['received_by']}}</td>
                        </tr>
                    @endforeach
                </table>

                @php
                    $grand_total_cost += $data[0]['cost_by_supplier'][$key][0]['total_cost'];
                    $grand_total_sell += $data[0]['cost_by_supplier'][$key][0]['total_sell'];
                    $grand_total_profit += $data[0]['cost_by_supplier'][$key][0]['profit'];
                @endphp

                <hr>

                <div class="full-row" style="padding-top: 1%">
                    <div class="col-35"><div class="full-row"></div></div>
                    <div class="col-15"></div>
                    <div class="col-25"></div>
                    <div class="col-25">
                        <div class="full-row">
                            <div class="col-50" align="left"><b>Total Buy</b></div>
                            <div class="col-50" align="right">{{number_format($data[0]['cost_by_supplier'][$key][0]['total_cost'],2)}}</div>
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
                            <div class="col-50" align="right">{{number_format($data[0]['cost_by_supplier'][$key][0]['total_sell'],2)}}</div>
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
                            <div class="col-50" align="right">{{number_format($data[0]['cost_by_supplier'][$key][0]['profit'],2)}}</div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- GRAND TOTAL SUMMARY - Centered like Cash Sales -->
            <div style="margin-top: 10px; padding-top: 5px;">
                <h3 align="center"><b>Total Summary</b></h3>
                <table style="min-width: 25%; width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 4px; text-align: right;"><b>Total Buy</b></td>
                        <td style="padding: 4px; text-align: center;"><b>:</b></td>
                        <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_total_cost, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px; text-align: right;"><b>Total Sales</b></td>
                        <td style="padding: 4px; text-align: center;"><b>:</b></td>
                        <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_total_sell, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 4px; text-align: right;"><b>Total Profit</b></td>
                        <td style="padding: 4px; text-align: center;"><b>:</b></td>
                        <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_total_profit, 2) }}</b></td>
                    </tr>
                </table>
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