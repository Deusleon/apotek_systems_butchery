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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Product Ledger Detailed Report</h3>
            <h4 align="center" style="margin-top: -1%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            <h4 align="center" style="margin-top: -1%">Product Name: {{$data[0]['name']}}</h4>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <div class="row" style="margin-top: 8%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white">
                            <th style="text-align: center;">#</th>
                            <th style="text-align: left">Date</th>
                            <th style="text-align: left">Transaction Method</th>
                            <th style="text-align: center">Received</th>
                            <th style="text-align: center">Outgoing</th>
                            <th style="text-align: center">Balance</th>
                            <th style="text-align: center">Created By</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td style="text-align: center">{{$loop->iteration}}.</td>
                            <td style="text-align: left">{{($item['date'] ?? '')}}</td>
                            <td style="text-align: left">{{$item['method']}}</td>
                            <td style="text-align: center;">
                                {{ is_numeric($item['received']) ? number_format((float) $item['received'], 0) : '' }}
                            </td>
                            <td style="text-align: center;">
                                {{ is_numeric($item['outgoing']) ? number_format((float) $item['outgoing'], 0) : '' }}
                            </td>
                            <td style="text-align: center;">
                                {{ is_numeric($item['balance']) ? number_format((float) $item['balance'], 0) : '' }}
                            </td>
                            <td style="text-align: left">{{$item['created_by']}}</td>
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