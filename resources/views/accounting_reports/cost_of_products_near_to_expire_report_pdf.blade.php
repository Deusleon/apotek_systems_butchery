<!DOCTYPE html>
<html>

<head>
    <title>Cost of Products Near to Expire Report</title>

    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th,
        td {
            /*border: 1px solid black;*/
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
            /*border-spacing: 5px;*/
            width: 100%;
            margin-top: -10%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        #table-detail tr> {
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
            padding-right: 5px;
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

        .col-30 {
            display: inline-block;
            font-size: 13px;
            width: 30%;
        }

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 35%;
        }

        .col-10 {
            display: inline-block;
            font-size: 13px;
            width: 10%;
        }

        .col-40 {
            display: inline-block;
            font-size: 13px;
            width: 40%;
            padding-top: 3px; 
            word-wrap: nowrap;
        }

        .col-60 {
            display: inline-block;
            font-size: 13px;
            width: 60%;
            word-wrap: nowrap;
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

    <div class="row" style="padding-top: -2%">
        <!-- Header Section -->
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
            @if($pharmacy['logo'])
                <img style="max-width: 90px; max-height: 90px;"
                    src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
            @endif
            <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
            <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
                {{$pharmacy['address']}}<br>
                {{$pharmacy['phone']}}<br>
                {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
            </div><br>
            <div>
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Cost of Products Near to Expire Report
                </h3>
                <h3 align="center" style="margin-top: -1%;">Expiry in
                    {{ $expMonth === 1 ? 'This Month' : ' next ' . $expMonth . ' months' }}
                </h3>
                <h4 align="center" style="margin-top: -1%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>

        <div class="row" style="margin-top: 7%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <!-- loop the product names here -->
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="left">Product Name</th>
                            <th align="left" style="min-width: 70px; white-space: nowrap;">Batch No.</th>
                            <th align="center">QOH</th>
                            <th align="center" style="min-width: 70px; white-space: nowrap;">Expiry Date</th>
                            <th align="right">Cost Buy</th>
                            <th align="right">Cost Sell</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td align="left">{{$item['product_name']}}</td>
                            <td align="left" style="min-width: 70px; white-space: nowrap;">{{$item['batch_number']}}</td>
                            <td align="center">
                                {{number_format($item['quantity'])}}
                            </td>
                            <td align="center" style="min-width: 70px; white-space: nowrap;">
                                {{date('Y-m-d', strtotime($item['expire_date']))}}
                            </td>
                            <td align="right">{{number_format($item['cost_buy_price'], 2)}}</td>
                            <td align="right">{{number_format($item['cost_sell_price'], 2)}}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <hr>
            <div class="full-row" style="padding-top: 1%">
                <div class="col-35">
                    <div class="full-row">
                    </div>
                </div>
                <div class="col-10"></div>
                <div class="col-25"></div>
                <div class="col-30">
                    <div class="full-row">
                        <div class="col-40" align="left"><b>Total Buy: </b></div>
                        <div class="col-60" align="right">{{number_format(max(array_column($data, 'total_buy')), 2)}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="full-row" style="padding-top: -1%">
                <div class="col-35">
                    <div class="full-row">
                    </div>
                </div>
                <div class="col-10"></div>
                <div class="col-25"></div>
                <div class="col-30">
                    <div class="full-row">
                        <div class="col-40" align="left"><b>Total Sell: </b></div>
                        <div class="col-60" align="right">{{number_format(max(array_column($data, 'total_sell')), 2)}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

</body>

</html>