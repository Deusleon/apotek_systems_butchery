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
            margin-bottom: 15%;
        }

        #table-detail-main {
            width: 102%;
            margin-top: -10%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr > {
            line-height: 13px;
        }

        #table-detail >tr:nth-child(even) {
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
<h2 align="center" style="margin-top: -2%">Material Received Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        @foreach($data_og[0]['data'] as $key => $items)
            <table id="table-detail-main">
                <tr>
                    {{--                <td style="background: #1f273b; color: white">Supplier: {{$data->first()->supplier_name}}</td>--}}
                    {{--                @if(!(empty($data->first()->invoice_nos)))--}}
                    {{--                    <td style="background: #1f273b; color: white">Invoice: {{$data->first()->invoice_nos}}</td>--}}
                    {{--                @endif--}}
                    {{--                <td style="background: #1f273b; color: white"><b>From--}}
                    {{--                        Date:</b> {{date('d-m-Y',strtotime($data->first()->dates[0]))}}</td>--}}
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
{{--            <table style="width: 103%; background-color: white; margin-top: -12%; margin-bottom: 4%">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tr>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td align="right" style="padding-top: -3%"><b>Total Buy:</b></td>--}}
{{--                    <td align="right"--}}
{{--                        style="padding-top: -3%">{{number_format($data_og[0]['total_cost'][$key][0]['amount'],2)}}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td align="right" style="padding-top: -3%"><b>Total Sell:</b></td>--}}
{{--                    <td align="right" style="padding-top: -3%">{{number_format($data_og[0]['total_sell'][$key][0]['amount'],2)}}</td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td></td>--}}
{{--                    <td align="right" style="padding-top: -3%"><b>Total Profit:</b></td>--}}
{{--                    <td align="right" style="padding-top: -3%">{{number_format($data_og[0]['total_profit'][$key][0]['amount'],2)}}</td>--}}
{{--                </tr>--}}
{{--            </table>--}}
        @endforeach
        {{--        <hr>--}}
        {{--        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Buy: </b>--}}
        {{--        </div>--}}
        {{--        <div align="right"--}}
        {{--             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->first()->total_bp,2)}}</div>--}}
        {{--        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Sell: </b>--}}
        {{--        </div>--}}
        {{--        <div align="right"--}}
        {{--             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->first()->total_sp,2)}}</div>--}}
        {{--        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Profit: </b>--}}
        {{--        </div>--}}
        {{--        <div align="right"--}}
        {{--             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->first()->total_p,2)}}</div>--}}
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

