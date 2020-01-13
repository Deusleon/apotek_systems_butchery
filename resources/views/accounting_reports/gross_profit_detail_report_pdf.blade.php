<!DOCTYPE html>
<html>
<head>
    <title>Gross Profit Detail Report</title>

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

        #table-detail-main {
            width: 103%;
            /*margin-top: 2%;*/
            margin-bottom: -3%;
            /*border-collapse: collapse;*/
        }

        #table-detail-main-1 {
            width: 103%;
            margin-top: 1%;
            margin-bottom: -1%;
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
<h2 align="center" style="margin-top: -2%">Gross Profit Detail Report</h2>
<div class="row">
    <div class="col-md-12">
        <table id="table-detail-main-1">
            <tr>
                <td style="background: #1f273b; color: white"><b>From
                        Date:</b> {{date('d-m-Y',strtotime($data[0]['from']))}}</td>
                <td style="background: #1f273b; color: white"><b>To
                        Date:</b> {{date('d-m-Y',strtotime($data[0]['to']))}}</td>
            </tr>
        </table>
        @foreach($data[0]['data'] as $key => $items)
            <table id="table-detail-main">
                <tr>
                    <td><b>Date:</b> {{date('d-m-Y',strtotime($key))}}</td>
                </tr>
            </table>

            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th>Product Name</th>
                    <th align="right">Buy Price</th>
                    <th align="right">Sell Price</th>
                    <th>Qty</th>
                    <th align="right">Amount</th>
                    <th align="right">Profit</th>
                </tr>
                </thead>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item['name']}}</td>
                        <td align="right">{{number_format($item['buy_price'],2)}}</td>
                        <td align="right">{{number_format($item['sell_price'],2)}}</td>
                        <td align="right">
                            <div style="margin-right: 50%">{{number_format($item['quantity'])}}</div>
                        </td>
                        <td align="right">{{number_format($item['amount'],2)}}</td>
                        <td align="right">{{number_format($item['profit'],2)}}</td>
                    </tr>
                @endforeach

            </table>
        @endforeach
        <hr>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Amount: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data[0]['total_amount'],2)}}</div>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Profit: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data[0]['total_profit'],2)}}</div>


        <hr>
        <h4 align="center"><b>Summary</b></h4>
        <table align="center">
            <thead>
            <tr>
                <th>Capital Invested</th>
                <td align="right">{{number_format($data[0]['total_buy'],2)}}</td>
            </tr>
            <tr>
                <th>Total Sales</th>
                <td align="right">{{number_format($data[0]['total_amount'],2)}}</td>

            </tr>
            <tr>
                <th>Total Profit</th>
                <td align="right">{{number_format($data[0]['total_profit'],2)}}</td>

            </tr>
            </thead>
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
