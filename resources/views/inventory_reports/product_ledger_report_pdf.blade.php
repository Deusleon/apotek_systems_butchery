<!DOCTYPE html>
<html>

<head>
    <title>Product Ledger Report</title>

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
            padding: 10px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
            margin-top: -1%;
            border: 1px solid #FFFFFF;
            border-collapse: collapse;
        }

        #table-detail tr {
            line-height: 14px;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }
    </style>

</head>

<body>

    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'] . ' | ' . $pharmacy['website']}}</h3>
        <h2 align="center" style="margin-top: -1%">Product Ledger Report</h2>
        <h3 align="center" style="margin-top: -1%">Product Name: {{$data[0]['name']}}</h3>

        <div class="row" style="">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white">
                            <th>#</th>
                            <th style="text-align: left">Date</th>
                            <th style="text-align: left">Transaction Method</th>
                            <th style="text-align: right">Received</th>
                            <th style="text-align: right">Outgoing</th>
                            <th style="text-align: right">Balance</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td style="text-align: center">{{$loop->iteration}}.</td>
                            <td style="text-align: left">{{$item['date']}}</td>
                            <td style="text-align: left">{{$item['method']}}</td>
                            <td style="text-align: right;">
                                {{number_format($item['received'], 2)}}
                            </td>
                            <td style="text-align: right;">
                                {{number_format($item['outgoing'], 2)}}
                            </td>
                            <td style="text-align: right;">{{number_format($item['balance'], 2)}}</td>
                        </tr>
                    @endforeach
                    {{-- @endforeach--}}
                </table>
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