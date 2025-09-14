<!DOCTYPE html>
<html>

<head>
    <title>Sale Return Report</title>

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
            /*margin-top: 2%;*/
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
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
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
        <h2 align="center" style="margin-top: -1%">Sale Return Report</h2>
        <h4 align="center" style="margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        <div class="row" style="margin-top: 2%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Product Name</th>
                            <th align="left">Buy Date</th>
                            <th align="center">Qty Bought</th>
                            <th align="left">Return Date</th>
                            <th align="center">Qty Returned</th>
                            <th align="left">Reason</th>
                            <th align="right">Refund</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($data) --}}
                        @php $totalRefund = 0; @endphp
                        @foreach($data as $datas)
                            <tr>
                                <td align="center">{{$loop->iteration}}.</td>
                                <td align="left">
                                    {{$datas['item_returned']['name']}}
                                    {{ $datas['item_returned']['brand'] ? ' ' . $datas['item_returned']['brand'] : '' }}
                                    {{ $datas['item_returned']['pack_size'] ?? '' }}{{ $datas['item_returned']['sales_uom'] ?? '' }}
                                </td>
                                <td align="left">{{date('Y-m-d', strtotime($datas['item_returned']['b_date']))}}</td>
                                @if($datas['status'] == 5)
                                    <td align="center">
                                        {{number_format(($datas['item_returned']['bought_qty'] + $datas['item_returned']['rtn_qty']), 0)}}
                                    </td>
                                @else
                                    <td align="center">{{number_format($datas['item_returned']['bought_qty'], 0)}}</td>
                                @endif

                                <td align="left">{{date('Y-m-d', strtotime($datas['date']))}}</td>
                                <td align="center">{{number_format($datas['item_returned']['rtn_qty'], 0)}}</td>
                                <td align="left">{{$datas['reason']}}</td>
                                <td align="right">{{number_format((($datas['item_returned']['rtn_qty']) /
                                ($datas['item_returned']['bought_qty'])) * ($datas['item_returned']['amount']
                                - $datas['item_returned']['discount']), 2)}}</td>
                            </tr>
                            @php
                                $refund = (($datas['item_returned']['rtn_qty']) / ($datas['item_returned']['bought_qty']))
                                            * ($datas['item_returned']['amount'] - $datas['item_returned']['discount']);
                                $totalRefund += $refund;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <div style="margin-top: 10px; padding-top: 5px;">
                    <h3 align="center">Overall Summary</h3>
                    <table style="width: 20%; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd;">
                        <tr>
                            <td align="right" style="padding: 8px; width: 50%;"><b>Total Refund:</b></td>
                            <td align="right" style="padding: 8px;">{{ number_format($totalRefund, 2) }}</td>
                        </tr>
                    </table>
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