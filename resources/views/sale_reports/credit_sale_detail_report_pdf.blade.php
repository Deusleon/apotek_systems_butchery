<!DOCTYPE html>
<html>

<head>
    <title>Credit Sales Details Report</title>
    <style>
        body {
            font-size: 13px;
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
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
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
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </h3>
        <h2 align="center" style="margin-top: -1%">Credit Sales Details Report</h2>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>
        {{-- @dd($data) --}}
        @foreach($data as $dat)
            <table id="table-detail-main">
                <tr>
                    <td><b>Date:</b> {{ date('Y-m-d', strtotime($dat['date'])) }}</td>
                </tr>
            </table>

            <table id="table-detail" align="center" style="margin-top: -1%; padding-top: 0%;">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center" style="width: 1%;">#</th>
                        <th align="left" style="width: 1%;">Receipt #</th>
                        <th align="left" style="width: 20%">Product Name</th>
                        <th align="left">Batch #</th>
                        <th align="left">Sold By</th>
                        <th align="center" style="width: 2%">Qty</th>
                        <th align="right">Sell Price</th>
                        <th align="right">Sub Total</th>
                        <th align="right">VAT</th>
                        @if ($enable_discount === 'YES')
                            <th align="right">Discount</th>
                        @endif
                        <th align="right">Amount</th>
                    </tr>
                </thead>

                <tbody>
                    @php $i = 1; @endphp
                    @foreach($dat['grouped_data'] as $itm)
                        <tr>
                            <td>{{$i++}}.</td>
                            <td>{{$itm['receipt']}}</td>
                            <td>{{$itm['name']}}</td>
                            <td>{{$itm['batch']}}</td>
                            <td>{{$itm['sold_by']}}</td>
                            <td align="center">{{ number_format($itm['quantity'], 0) }}</td>
                            <td align="right">{{ number_format($itm['price'], 2) }}</td>
                            <td align="right">{{ number_format($itm['sub_total'], 2) }}</td>
                            <td align="right">{{ number_format($itm['vat'], 2) }}</td>
                            <td align="right">{{ number_format($itm['discount'], 2) }}</td>
                            <td align="right">{{ number_format($itm['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table style="width: 101%; margin-bottom: 5%;">
                <tr>
                    <td align="right" style="padding-top: -4%; width: 60%;"><b>Sub Total:</b></td>
                    <td align="right" style="padding-top: -4%;">
                        {{ number_format(($dat['totals']['grand_total'] - $dat['totals']['total_vat']), 2) }}
                    </td>
                    <td align="right" style="padding-top: -4%; width: 20%;"><b>Paid:</b></td>
                    <td align="right" style="padding-top: -4%">{{ number_format($dat['totals']['total_paid'], 2) }}</td>
                </tr>
                <tr>
                    <td align="right" style="padding-top: -4%; width: 60%;"><b>VAT:</b></td>
                    <td align="right" style="padding-top: -4%">{{ number_format($dat['totals']['total_vat'], 2) }}</td>
                    <td align="right" style="padding-top: -4%; width: 20%;"><b>Balance:</b></td>
                    <td align="right" style="padding-top: -4%">{{ number_format($dat['totals']['total_balance'], 2) }}</td>
                </tr>
                @if ($enable_discount === 'YES')
                    <tr>
                        <td align="right" style="padding-top: -3%; width: 40%;"><b>Discount:</b></td>
                        <td align="right" style="padding-top: -3%">{{ number_format($dat['totals']['total_discount'], 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td align="right" style="padding-top: -3%; width: 40%;"><b>Total:</b></td>
                    <td align="right" style="padding-top: -3%">{{ number_format($dat['totals']['grand_total'], 2) }}</td>
                </tr>
            </table>
            <hr>
        @endforeach
        {{-- compute overall totals in blade --}}
        @php
            $overallTotals = [
                'total_count' => 0,
                'grand_subtotal' => 0.0,
                'grand_total' => 0.0,
                'total_paid' => 0.0,
                'total_balance' => 0.0,
                'total_vat' => 0.0,
                'total_discount' => 0.0,
            ];

            foreach ($data as $day) {
                $tot = $day['totals'] ?? [];

                // determine sales count for the day
                if (isset($tot['count_sales'])) {
                    $count = (int) $tot['count_sales'];
                } elseif (isset($tot['countSales'])) {
                    $count = (int) $tot['countSales'];
                } elseif (isset($day['sales']) && is_array($day['sales'])) {
                    $count = count($day['sales']);
                } else {
                    $count = 0;
                }

                $overallTotals['total_count'] += $count;
                $overallTotals['grand_total'] += (float) ($tot['grand_total'] ?? 0);
                $overallTotals['grand_subtotal'] += (float) (($tot['grand_total'] ?? 0)-($tot['total_vat'] ?? 0));
                $overallTotals['total_paid'] += (float) ($tot['total_paid'] ?? 0);
                $overallTotals['total_balance'] += (float) ($tot['total_balance'] ?? 0);
                $overallTotals['total_vat'] += (float) ($tot['total_vat'] ?? 0);
                $overallTotals['total_discount'] += (float) ($tot['total_discount'] ?? 0);
            }

            // round for neatness
            $overallTotals['grand_subtotal'] = round($overallTotals['grand_subtotal'], 2);
            $overallTotals['grand_total'] = round($overallTotals['grand_total'], 2);
            $overallTotals['total_paid'] = round($overallTotals['total_paid'], 2);
            $overallTotals['total_balance'] = round($overallTotals['total_balance'], 2);
            $overallTotals['total_vat'] = round($overallTotals['total_vat'], 2);
            $overallTotals['total_discount'] = round($overallTotals['total_discount'], 2);
        @endphp

        <div style="margin-top: 10px; padding-top: 5px;">
            <h3 align="center"><b>Total Summary</b></h3>
            <table
                style="width: auto; min-width: 25%; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>Subtotal</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right;">
                        <b>{{ number_format($overallTotals['grand_subtotal'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>VAT</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right;">
                        <b>{{ number_format($overallTotals['total_vat'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>Discount</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right;">
                        <b>{{ number_format($overallTotals['total_discount'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>Total Amount</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right;">
                        <b>{{ number_format($overallTotals['grand_total'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>Paid</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right;">
                        <b>{{ number_format($overallTotals['total_paid'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: right;"><b>Balance</b></td>
                    <td style="padding: 8px; text-align: center;"><b>:</b></td>
                    <td style="padding: 8px; text-align: right; color: red;">
                        <b>{{ number_format($overallTotals['total_balance'], 2) }}</b></td>
                </tr>
            </table>
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