<!DOCTYPE html>
<html>
<head>
    <title>Material Received Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

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
            margin-bottom: 0%;
        }

        #table-detail-main {
            width: 102%;
            margin-top: 1%;
            margin-bottom: -2%;
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
<h5 align="center" style="margin-top: -2%">Phone: {{$pharmacy['phone']}}</h5>
<h2 align="center" style="margin-top: -2%">Material Received Report</h2>
<div class="row">
    <div class="col-md-12">
        @foreach($data_og[0]['data'] as $key => $items)
            <table id="table-detail-main">
                <tr>
                    <td style="background: #1f273b; color: white"><b>Supplier: </b>{{$key}}</td>
                </tr>
            </table>

            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th>Code</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th align="right">Buy Price</th>
                    <th align="right">Sell Price</th>
                    <th align="right">Profit</th>
                    <th>Receive Date</th>

                </tr>
                </thead>
                @foreach($items as $item)
                    <tr>
                        <td>{{$item['code']}}</td>
                        <td>{{$item['product_name']}}</td>
                        <td align="right">
                            <div style="margin-right: 50%">{{number_format($item['quantity'])}}</div>
                        </td>
                        <td align="right">{{number_format($item['unit_cost'],2)}}</td>
                        <td align="right">{{number_format($item['sell_price'],2)}}</td>
                        <td align="right">{{number_format($item['profit'],2)}}</td>
                        <td>{{date('d-m-Y',strtotime($item['date']))}}
                        </td>
                    </tr>
                @endforeach
            </table>

            <hr>
            <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total
                    Buy: </b>
            </div>
            <div align="right"
                 style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
                {{number_format($data_og[0]['cost_by_supplier'][$key][0]['total_cost'],2)}}</div>
            <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total
                    Sell: </b>
            </div>
            <div align="right"
                 style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
                {{number_format($data_og[0]['cost_by_supplier'][$key][0]['total_sell'],2)}}</div>
            <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total
                    Profit: </b>
            </div>
            <div align="right"
                 style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data_og[0]['cost_by_supplier'][$key][0]['profit'],2)}}</div>

        @endforeach
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
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }







</script>

</body>
</html>

