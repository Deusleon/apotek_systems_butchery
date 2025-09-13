<!DOCTYPE html>
<html>

<head>
    <title>Credit Sale Summary Report</title>
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
        <h2 align="center" style="margin-top: -1%">Credit Sales Summary Report</h2>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        <div class="row" style="">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center">#</th>
                        <th align="left">Receipt #</th>
                        <th align="left">Customer Name</th>
                        <th align="right">Total</th>
                        <th align="right">Paid</th>
                        <th align="right">Balance</th>
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
                            <td align="right">{{number_format($item['total'], 2)}}</td>
                            <td align="right">{{number_format($item['paid'], 2)}}</td>
                            <td align="right">{{number_format($item['balance'], 2)}}</td>
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
            <hr>
            <div style="margin-top: 10px; padding-top: 5px;">
                <h3 align="center">Overall Summary</h3>
                <table style="width: 30%; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd;">
                    {{-- <tr style="background: #f8f9fa;">
                        <td align="right" style="padding: 8px; width: 70%;"><b>Total Sales Transactions:</b></td>
                        <td align="right" style="padding: 8px;">{{ number_format($overallTotals['total_count'], 0) }}
                        </td>
                    </tr> --}}
                    <tr>
                        <td align="right" style="padding: 8px; width: 50%;"><b>Total Amount:</b></td>
                        <td align="right" style="padding: 8px;">{{ number_format($data['grand_total'], 2) }}</td>
                    </tr>
                    <tr>
                        <td align="right" style="padding: 8px; width: 50%;"><b>Total Paid:</b></td>
                        <td align="right" style="padding: 8px;">{{ number_format($data['total_paid'], 2) }}</td>
                    </tr>
                    <tr>
                        <td align="right" style="padding: 8px; width: 50%;"><b>Outstanding Balance:</b></td>
                        <td align="right" style="padding: 8px; color: red;">
                            {{ number_format($data['total_balance'], 2) }}
                        </td>
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