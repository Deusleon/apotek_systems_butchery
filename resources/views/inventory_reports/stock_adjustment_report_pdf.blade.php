<!DOCTYPE html>
<html>

<head>
    <title>Stock Adjustment Report</title>

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

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            /*border-spacing: 5px;*/
            width: 100%;
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
    </style>

</head>

<body>

    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'] . ' | ' . $pharmacy['website']}}</h3>
        <h2 align="center" style="margin-top: -1%">Stock Adjustment Report</h2>
        @if ($type)
            <h3 align="center" style="margin-top: -1%">
                @if ($data[0]['type'] == 'increase') Positive @else Negative
                @endif Adjustment
            </h3>
        @endif
        <h3 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d', strtotime($data[0]['dates'][0]))}}</b> To:
            <b>{{date('Y-m-d', strtotime($data[0]['dates'][1]))}}</b>
        </h3>
        <div class="row">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Product Name</th>
                            @if (!$type)
                                <th align="left">Type</th>
                            @endif
                            <th align="center">Quantity</th>
                            <th align="left">Reason</th>
                            <th align="left">Adjusted By</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td align="center">{{$loop->iteration}}.</td>
                            <td align="left">{{$item['name']}}</td>
                            @if (!$type)
                                <td align="left">
                                    @if ($item['type'] == 'increase') Positive @else Negative
                                    @endif
                                </td>
                            @endif
                            <td align="center">{{number_format($item['quantity'])}}</td>
                            <td align="left">{{$item['reason']}}</td>
                            <td align="left">{{$item['adjusted_by']}}</td>
                        </tr>
                    @endforeach
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