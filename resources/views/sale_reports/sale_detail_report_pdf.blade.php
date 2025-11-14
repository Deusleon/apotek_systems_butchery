<!DOCTYPE html>
<html>

<head>
    <title>Sales Details Report</title>

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
            padding-left: 0%;
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
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -6%;">
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
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Sales Details Report</h3>
                <h4 align="center" style="margin-top: -1%">From: <b>{{$pharmacy['from_date']}}</b> To:
                    <b>{{$pharmacy['to_date']}}</b>
                </h4>
                <h4 align="center" style="margin-top: -2%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>
        @php
            $grand_sub_total = 0;
            $grand_vat_total = 0;
            $grand_discount_total = 0;
            $grand_amount_total = 0;
        @endphp
        {{-- @dd($data) --}}
        @foreach($data[0][0] as $key => $dat)
            {{-- {{$pharmacy['tin_number']}} {{date('j M, Y', strtotime($dat[0]['created_at']))}}--}}
            <table id="table-detail-main">
                <tr>
                    <td><b>Date:</b> {{date('Y-m-d', strtotime($dat[0]['created_at']))}}</td>
                </tr>
            </table>
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="left" style="width: 1.5%;">#</th>
                        <th align="left" style="width: 7%">Receipt #</th>
                        <th align="left" style="width: 20%">Product Name</th>
                        <th align="left" style="width: 7%;">Batch #</th>
                        <th align="left" style="width: 11%;">Sold By</th>
                        <th align="center" style="width: 2%">Qty</th>
                        <th align="right">Sales Price</th>
                        <th align="right">Sub Total</th>
                        <th align="right">VAT</th>
                        @if ($enable_discount === 'YES')
                            <th align="right">Discount</th>
                        @endif
                        <th align="right">Amount</th>
                    </tr>
                </thead>
                @foreach($dat as $item)
                    <tr>
                        <td align="left">{{$loop->iteration}}.</td>
                        <td align="left">{{$item['receipt_number']}}</td>
                        <td align="left">{{$item['name']}}</td>
                        <td align="left">{{$item['batch_number']}}</td>
                        <td align="left">{{$item['sold_by']}}</td>
                        <td align="center">{{number_format($item['quantity'], 0)}}</td>
                        <td align="right">{{number_format($item['price'], 2)}}</td>
                        <td align="right">{{number_format($item['sub_total'], 2)}}</td>
                        <td align="right">{{number_format($item['vat'], 2)}}</td>
                        @if ($enable_discount === 'YES')
                            <td align="right">{{number_format($item['discount'], 2)}}</td>
                        @endif
                        <td align="right">{{number_format($item['amount'], 2)}}</td>
                    </tr>
                @endforeach
            </table>

            @foreach($data[0][2][$key] as $e)
                {{-- {{number_format($e['test_total'],2)}}--}}
                <table style="width: 101%;">
                    <tr>
                        <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Sub Total:</b></td>
                        <td align="right" style="padding-top: -3%;">
                            {{number_format($e['amount_total'] - $e['vat_total'] + $e['discount_total'], 2)}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>VAT:</b></td>
                        <td align="right" style="padding-top: -3%">{{number_format($e['vat_total'], 2)}}</td>
                    </tr>
                    @if ($enable_discount === 'YES')
                        <tr>
                            <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Discount:</b></td>
                            <td align="right" style="padding-top: -3%">{{number_format($e['discount_total'], 2)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="10" align="right" style="padding-top: -3%; width: 85%;"><b>Total:</b></td>
                        <td align="right" style="padding-top: -3%">{{number_format($e['amount_total'], 2)}}</td>
                    </tr>
                </table>
                @php
                    $grand_sub_total += $e['amount_total'] - $e['vat_total'] + $e['discount_total'];
                    $grand_vat_total += $e['vat_total'];
                    $grand_discount_total += $e['discount_total'];
                    $grand_amount_total += $e['amount_total'];
                @endphp
            @endforeach
            <hr style="margin-left: 5px;">
        @endforeach
        <div style="margin-top: 10px; padding-top: 5px;">
            <h3 align="center"><b>Summary</b></h3>
            <table
                style="min-width: 25%; width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Sub Total</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_sub_total, 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>VAT</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_vat_total, 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Discount</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_discount_total, 2) }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Total</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($grand_amount_total, 2) }}</b></td>
                </tr>
            </table>
        </div>

    </div>

    <script type="text/php">

</script>

</body>

</html>