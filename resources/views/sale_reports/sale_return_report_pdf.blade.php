<!DOCTYPE html>
<html>

<head>
    <title>Sales Returns Report</title>

    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th {
            border-collapse: collapse;
            padding: 8px;
        }

        table,
        td {
            border-collapse: collapse;
            padding: 5px;
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
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 10px;
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
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>

<body>
    <div class="row" style="padding-top: -2%">
        <!-- Header Section -->
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -3%;">
            @if($pharmacy['logo'])
                <img style="max-width: 90px; max-height: 90px;"
                    src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
            @endif
            <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
            <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
                {{$pharmacy['address']}}<br>
                {{$pharmacy['phone']}}<br>
                {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
            </div><br>
            <div>
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Sales Returns Report</h3>
                <h4 align="center" style="margin-top: -1%">From: <b>{{$pharmacy['from_date']}}</b> To:
                    <b>{{$pharmacy['to_date']}}</b>
                </h4>
                <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>
        <div class="row" style="margin-top: 2%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="center">Product Name</th>
                            <th align="left">Sales Date</th>
                            <th align="center">Qty Bought</th>
                            <th align="center">Return Date</th>
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
                                                <td align="center">{{date('Y-m-d', strtotime($datas['item_returned']['b_date']))}}</td>
                                                {{-- @if($datas['status'] == 5) --}}
                                                <td align="center">
                                                    {{number_format(($datas['item_returned']['remained_qty'] + $datas['item_returned']['rtn_qty']), 0)}}
                                                </td>
                                                {{-- @else
                                                <td align="center">{{number_format($datas['item_returned']['remained_qty'], 0)}}</td>
                                                @endif --}}

                                                <td align="center">{{date('Y-m-d', strtotime($datas['date']))}}</td>
                                                <td align="center">{{number_format($datas['item_returned']['rtn_qty'], 0)}}</td>
                                                <td align="left">{{$datas['reason']}}</td>
                                                <td align="right">{{number_format((($datas['item_returned']['rtn_qty']) /
                            ($datas['item_returned']['remained_qty'])) * ($datas['item_returned']['amount']
                            - $datas['item_returned']['discount']), 2)}}</td>
                                            </tr>
                                            @php
                                                $refund = (($datas['item_returned']['rtn_qty']) / ($datas['item_returned']['remained_qty']))
                                                    * ($datas['item_returned']['amount'] - $datas['item_returned']['discount']);
                                                $totalRefund += $refund;
                                            @endphp
                        @endforeach
                    </tbody>
                </table>
                <hr style="margin-left: 10px;">
                <div style="margin-top: 10px; padding-top: 5px;">
                    <h3 align="center"><b>Summary</b></h3>
                    <table
                        style="width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; text-align: right;"><b>Total</b></td>
                            <td style="padding: 8px; text-align: center;"><b>:</b></td>
                            <td style="padding: 8px; text-align: right;"><b>{{ number_format($totalRefund, 2) }}</b>
                            </td>
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