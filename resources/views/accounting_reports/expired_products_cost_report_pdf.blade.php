<!DOCTYPE html>
<html>
<head>
    <title>Expired Product Cost Report</title>

    <style>

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
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
            margin-top: -10%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
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


    </style>

</head>
<body>

<div class="row" style="padding-top: -2%">
    <h1 align="center">{{$pharmacy['name']}}</h1>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'].' | '.$pharmacy['website']}}</h3>
    <h2 align="center" style="margin-top: -1%">Expired Product Cost Report</h2>
    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th>Product Name</th>
                    <th>Batch No.</th>
                    <th>QOH</th>
                    <th align="right">Cost Buy</th>
                    <th align="right">Cost Sell</th>
                    <th>Expire Date</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td>{{$item['name']}}</td>
                        <td>{{$item['batch_number']}}</td>
                        <td align="right">
                            <div style="margin-right: 50%">{{number_format($item['quantity'])}}</div>
                        </td>
                        <td align="right">{{number_format($item['cost_buy_price'],2)}}</td>
                        <td align="right">{{number_format($item['cost_sell_price'],2)}}</td>
                        <td>{{$item['expire_date']}}</td>
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
            <div class="col-15"></div>
            <div class="col-25"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Total Buy: </b></div>
                    <div class="col-50"
                         align="right">{{number_format(max(array_column($data, 'total_buy')),2)}}</div>
                </div>
            </div>
        </div>
        <div class="full-row" style="padding-top: 1%">
            <div class="col-35">
                <div class="full-row">
                </div>

            </div>
            <div class="col-15"></div>
            <div class="col-25"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Total Sell: </b></div>
                    <div class="col-50"
                         align="right">{{number_format(max(array_column($data, 'total_sell')),2)}}</div>
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

