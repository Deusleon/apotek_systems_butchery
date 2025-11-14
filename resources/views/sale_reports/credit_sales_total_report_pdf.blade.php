<!DOCTYPE html>
<html>

<head>
    <title>Credit Sales Total Report</title>

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
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
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
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Credit Sales Total Report</h3>
                <h4 align="center" style="margin-top: -1%">From: <b>{{$pharmacy['from_date']}}</b> To:
                    <b>{{$pharmacy['to_date']}}</b>
                </h4>
                <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>
        <div class="row" style="">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center" style="width: 5%;">#</th>
                        <th align="left" style="width: 100px;">Date</th>
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