<!DOCTYPE html>
<html>
<head>
    <title>Gross Profit Summary Report</title>

    <style>

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 5px;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        #table-detail {
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        tr:nth-child(even) {
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
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;"
                src="{{ public_path('fileStore/logo/' . $pharmacy['logo']) }}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{ $pharmacy['name'] }}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{ $pharmacy['address'] }}<br>
            {{ $pharmacy['phone'] }}<br>
            {{ $pharmacy['email'] . ' | ' . $pharmacy['website'] }}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Gross Profit Summary Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{ date('Y-m-d', strtotime($data[0]['from'])) }}</b> To:
                <b>{{ date('Y-m-d', strtotime($data[0]['to'])) }}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 5px;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="center" style="width: 5%;">#</th>
                        <th align="left" style="width: 25%;">Date</th>
                        <th align="right" style="width: 25%;">Total Buy</th>
                        <th align="right" style="width: 25%;">Total Sales</th>
                        <th align="right" style="width: 25%;">Total Profit</th>
                    </tr>
                </thead>
                @foreach($data[0]['dates'] as $items)
                    <tr>
                        <td align="center" style="width: 5%;">{{$loop->iteration}}</td>
                        <td align="left" style="width: 25%;">{{ date('Y-m-d', strtotime($items)) }}</td>
                        @php
                            $total_buy  = $data[0]['total_buy'][$items][0]['total_buy'] ?? 0;
                            $total_sell = $data[0]['total_sell'][$items][0]['total_sell'] ?? 0;
                        @endphp
                        <td align="right" style="width: 25%;">{{ number_format($total_buy, 2) }}</td>
                        <td align="right" style="width: 25%;">{{ number_format($total_sell, 2) }}</td>
                        <td align="right" style="width: 25%;">{{ number_format($total_sell - $total_buy, 2) }}</td>
                    </tr>
                @endforeach
            </table>

            <hr>

            <div style="margin-left: 70%;width: 29.6%;margin-top: 0.5%; padding: 1%"><b>Total Buy: </b></div>
            <div align="right" style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
                {{ number_format($data[0]['grand_total_buy'], 2) }}
            </div>

            <div style="margin-left: 70%;width: 29.6%;margin-top: 0.5%; padding: 1%"><b>Total Sales: </b></div>
            <div align="right" style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
                {{ number_format($data[0]['grand_total_sell'], 2) }}
            </div>

            <div style="margin-left: 70%;width: 29.6%;margin-top: 0.5%; padding: 1%"><b>Total Profit: </b></div>
            <div align="right" style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
                {{ number_format($data[0]['grand_total_sell'] - $data[0]['grand_total_buy'], 2) }}
            </div>

        </div>
    </div>
</div>

<script type="text/php">
    if (isset($pdf)) {
        $x = 280;
        $y = 820;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>

</body>
</html>