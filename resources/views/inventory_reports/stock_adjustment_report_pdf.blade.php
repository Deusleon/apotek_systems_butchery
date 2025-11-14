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
            border-collapse: collapse;
            padding: 8px;
        }

        th {
            text-align: left;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group;
            background: #1f273b;
            color: white;
            font-size: 12px;
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
            margin-top: -13%;
            border-collapse: collapse;
        }

        #table-detail tr {
            line-height: 10px;
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

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>

</head>

<body>
    <!-- Header Section -->
    <div style="width: 100%; text-align: center; align-items: center;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;" src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Stock Adjustment Report</h3>
            @if ($type)
                <h4 align="center" style="margin-top: -1%">
                    @if ($data[0]['type'] == 'increase') Positive @else Negative
                    @endif Adjustment
                </h4>
            @endif
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d', strtotime($data[0]['dates'][0]))}}</b>
                To:
                <b>{{date('Y-m-d', strtotime($data[0]['dates'][1]))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <div class="row" style="margin-top: 8%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center" style="text-align: center; width: 2%;">#</th>
                            <th align="left" style="width: 35%;">Product Name</th>
                            @if (!$type)
                                <th align="left">Type</th>
                            @endif
                            <th align="center" style="text-align: center;">Quantity</th>
                            <th align="left">Reason</th>
                            <th align="center" style="text-align: center;">Adjustment Date</th>
                            <th align="left" style="width: 15%;">Adjusted By</th>
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
                            <td align="center">{{date('Y-m-d', strtotime($item['date']))}}</td>
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