<!DOCTYPE html>
<html>

<head>
    <title>Credit Total Report</title>
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

        #table-detail tr> {
            line-height: 20px;
        }

        #table-detail tbody tr:nth-child(even) {
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
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </h3>
        <h2 align="center" style="margin-top: -1%">Credit Sales Total Report</h2>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        <div class="row" style="">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center">#</th>
                        <th align="left">Date</th>
                        <th align="right">Sub Total</th>
                        <th align="right">VAT</th>
                        @if ($enable_discount === 'YES')
                            <th align="right">Discount</th>
                        @endif
                        <th align="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $x = 0; ?>
                    <?php $total_sub_total = 0;?>
                    <?php $total_vat = 0;?>
                    <?php $total_discount = 0;?>
                    <?php $grand_total = 0;?>
                    {{-- @dd($data) --}}
                    @foreach($data as $item)
                        <tr>
                            <td align="center">{{ $loop->iteration }}.</td>
                            <td align="left">{{date('Y-m-d', strtotime($item['date']))}}</td>
                            <td align="right">
                                <div>{{number_format($item['sub_total'], 2)}}</div>
                            </td>
                            <td align="right">{{number_format($item['vat'], 2)}}</td>
                            @if ($enable_discount === 'YES')
                                <td align="right">{{number_format($item['discount'], 2)}}</td>
                            @endif
                            <td align="right">{{number_format($item['total'], 2)}}</td>

                        </tr>
                        <?php    $total_sub_total += $item['sub_total'];?>
                        <?php    $total_vat += $item['vat'];?>
                        <?php    $total_discount += $item['discount'];?>
                        <?php    $grand_total += $item['total'] - $item['discount'];?>
                    @endforeach
                </tbody>
            </table>
            <table style="width: 101%;">
                <tr>
                    <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Sub Total:</b></td>
                    <td align="right" style="padding-top: -3%;">
                        {{number_format($total_sub_total, 2)}}
                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>VAT:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($total_vat, 2)}}</td>
                </tr>
                @if ($enable_discount === 'YES')
                    <tr>
                        <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Discount:</b></td>
                        <td align="right" style="padding-top: -3%">{{number_format($total_discount, 2)}}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Total:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($grand_total, 2)}}</td>
                </tr>
            </table>
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