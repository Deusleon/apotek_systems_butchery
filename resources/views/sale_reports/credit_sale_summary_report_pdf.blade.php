<!DOCTYPE html>
<html>

<head>
    <title>Credit Sales Summary Report</title>
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

        hr{
            width: 100.005%;
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
        <h2 align="center" style="margin-top: -1%">Credit Sales Summary Report</h2>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        <div class="row" style="">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center">#</th>
                        <th align="left">Receipt #</th>
                        <th align="left">Customer Name</th>
                        <th align="left">Date</th>
                        <th align="right">Total</th>
                        <th align="right">Paid</th>
                        <th align="right" style="padding-right: 50px;">Balance</th>
                        <th align="left">Sold By</th>
                        <th align="left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @dd($data) --}}
                    @foreach($data['info'] as $item)
                        <tr>
                            <td align="center">{{ $loop->iteration }}.</td>
                            <td align="left">{{ $item['receipt_number'] }}</td>
                            <td align="left">{{$item['customer_name']}}</td>
                            <td align="left">{{ date('Y-m-d', strtotime($item['sales_date'])) }}</td>
                            <td align="right">{{number_format($item['total'], 2)}}</td>
                            <td align="right">{{number_format($item['paid'], 2)}}</td>
                            <td align="right" style="padding-right: 50px;">{{number_format($item['balance'], 2)}}</td>
                            <td align="left">{{$item['sold_by']}}</td>
                            <td align="left">
                                @if ($item['status'] === 'Unpaid')
                                    <span style="color: red;">{{$item['status']}}</span>
                                @elseif($item['status'] === 'Paid')
                                    <span style="color: rgb(2, 202, 2);">{{$item['status']}}</span>
                                @else
                                    <span>{{$item['status']}}</span>
                                @endif
                            </td>

                        </tr>

                    @endforeach
                </tbody>
            </table>
                <hr style="margin-left: 5px;">
            <div style="margin-top: 10px; padding-top: 5px;">
                <h3 align="center"><b>Summary</b></h3>
                <table
                    style="width: auto; min-width: 200px; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; text-align: right;"><b>Total</b></td>
                        <td style="padding: 8px; text-align: center;"><b>:</b></td>
                        <td style="padding: 8px; text-align: right;"><b>{{ number_format($data['grand_total'], 2) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: right;"><b>Paid</b></td>
                        <td style="padding: 8px; text-align: center;"><b>:</b></td>
                        <td style="padding: 8px; text-align: right;"><b>{{ number_format($data['total_paid'], 2) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; text-align: right;"><b>Balance</b></td>
                        <td style="padding: 8px; text-align: center;"><b>:</b></td>
                        <td style="padding: 8px; text-align: right; color: red;">
                            <b>{{ number_format($data['total_balance'], 2) }}</b></td>
                    </tr>
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