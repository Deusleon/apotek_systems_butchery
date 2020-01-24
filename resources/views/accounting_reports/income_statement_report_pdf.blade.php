<!DOCTYPE html>
<html>
<head>
    <title>Income Statement Report</title>

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
            /*margin-top: -10%;*/
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

    </style>

</head>
<body>

<h4 align="center">{{$pharmacy['name']}}</h4>
<h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
<h2 align="center" style="margin-top: -2%">Income Statement Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail-main">
            <tr>
                <td style="background: #1f273b; color: white"><b>From
                        Date:</b> {{date('d-m-Y',strtotime($data->first()->from))}}</td>
                <td style="background: #1f273b; color: white"><b>To
                        Date:</b> {{date('d-m-Y',strtotime($data->first()->to))}}</td>
            </tr>
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white;">
                <th>Sales</th>
                <th></th>
            </tr>
            </thead>
            <tr>
                <td align="right">Sale Amount</td>
                <td align="right">{{number_format($data->last()->total_sell,2)}}</td>
            </tr>
        </table>
        <hr>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Sell: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
            {{number_format($data->last()->total_sell,2)}}</div>


        <table id="table-detail-1" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white;">
                <th>Cost of Sales</th>
                <th></th>
            </tr>
            </thead>
            <tr>
                <td align="right">Buy Amount</td>
                <td align="right">{{number_format($data->last()->total_buy,2)}}</td>
            </tr>
        </table>
        <hr>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Buy: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
            {{number_format($data->last()->total_buy,2)}}</div>

        <hr style="margin-top: 3%">
        <table id="table-detail-2" align="center">
            <!-- loop the product names here -->
            <tr>
                <td align="right">Net Income (Total Sell - Total Buy - Expenses)</td>
                <td align="right">{{number_format(($data->last()->total_sell) - ($data->last()->total_buy) - ($data->last()->expense_amount),2)}}</td>
            </tr>
        </table>
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

