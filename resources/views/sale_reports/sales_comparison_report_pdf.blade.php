<!DOCTYPE html>
<html>

<head>
    <title>Sales Comparison Report</title>

    <style>
        body {
            font-size: 13px;
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

    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'] . ' | ' . $pharmacy['website']}}</h3>
        <h2 align="center" style="margin-top: -1%">Sales Comparison Report</h2>
        <h4 align="center" style="margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        <div class="row" style="margin-top: 2%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <!-- loop the product names here -->
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center" style="width: 1%;">#</th>
                            <th align="left">Date</th>
                            @foreach($data[0]['data'] as $key => $items)
                                <th align="right">{{$key}}</th>
                            @endforeach
                            <th align="right">Total</th>

                        </tr>
                    </thead>
                    {{-- @dd($data) --}}
                    @foreach($data[0]['dates'] as $items)
                        <tr>
                            <td align="center">{{$loop->iteration}}.</td>
                            <td align="left">{{date('Y-m-d', strtotime($items))}}</td>
                            @foreach($data[0]['data'] as $keys => $item)
                                <td align="right">{{number_format($data[0]['data'][$keys][$items], 2)}}</td>
                            @endforeach
                            <td align="right"><b>{{number_format($data[0]['sum_by_date'][$items][0]['amount'], 2)}}</b></td>

                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><b>Total</b></td>
                        @foreach($data[0]['data'] as $keys => $item)
                            <td align="right"><b>{{number_format($data[0]['sum_by_user'][$keys][0]['amount'], 2)}}</b></td>
                        @endforeach
                        <td align="right"><b>{{number_format($data[0]['grand_total'], 2)}}</b></td>
                    </tr>
                </table>
                <hr>
                @php
                    $sumByUser = $data[0]['sum_by_user'];
                    $grandTotal = $data[0]['grand_total'];

                    $topSellers = [];
                    foreach ($sumByUser as $user => $entries) {
                        $total = 0;
                        foreach ($entries as $entry) {
                            $total += $entry['amount'];
                        }
                        $percentage = $grandTotal > 0 ? ($total / $grandTotal) * 100 : 0;
                        $topSellers[] = [
                            'user' => $user,
                            'total_sales' => $total,
                            'percentage' => $percentage
                        ];
                    }

                    // sort descending by total_sales
                    usort($topSellers, function ($a, $b) {
                        return $b['total_sales'] <=> $a['total_sales'];
                    });

                    // take top 3
                    $topSellers = array_slice($topSellers, 0);
                @endphp

                <div style="margin-top: 10px; padding-top: 5px;">
                    <h3 align="center"><b>Top Sellers</b></h3>
                    <table
                        style="width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px;"><b>#</b></td>
                            <td style="padding: 8px; text-align: left;"><b>Name:</b></td>
                            <td style="padding: 8px; text-align: center;"><b>:</b></td>
                            <td style="padding: 8px; text-align: right;">
                                <b>Sales</b>
                            </td>
                            <td style="padding: 8px; text-align: center;"><b>:</b></td>
                            <td style="padding: 8px; text-align: right;">
                                <b>%</b>
                            </td>
                        </tr>
                        @foreach($topSellers as $seller)
                            <tr>
                                <td style="padding: 8px;"><b>{{ $loop->iteration }}.</b></td>
                                <td style="padding: 8px; text-align: left;"><b>{{ $seller['user'] }}</b></td>
                                <td style="padding: 8px; text-align: center;"><b>:</b></td>
                                <td style="padding: 8px; text-align: right;">
                                    <b>{{ number_format($seller['total_sales'], 2) }}</b>
                                </td>
                                <td style="padding: 8px; text-align: center;"><b>:</b></td>
                                <td style="padding: 8px; text-align: right;">
                                    <b>{{ number_format($seller['percentage'], 2) }}%</b>
                                </td>
                            </tr>
                        @endforeach
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