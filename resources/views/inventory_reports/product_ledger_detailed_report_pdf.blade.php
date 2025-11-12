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
    <div class="row">
        <div id="container">
            <div class="logo-container">
                @if($pharmacy['logo'])
                    <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
                @endif
            </div>
        </div>
    </div>
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