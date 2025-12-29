<!DOCTYPE html>
<html>

<head>
    <title>Production Report</title>
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

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h3,
        h4 {
            font-weight: normal;
            text-align: center;
            margin: 0;
        }

        .logo-container {
            text-align: center;
        }

        .logo-container img {
            max-width: 90px;
            max-height: 90px;
        }
    </style>
</head>

<body>
    <div class="row" style="padding-top: -2%">
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -2%;">
            @if(isset($pharmacy) && isset($pharmacy['logo']) && $pharmacy['logo'])
                <img style="max-width: 90px; max-height: 90px;"
                    src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
            @endif
            <div style="font-weight: bold; font-size: 16px;">{{ $pharmacy['name'] ?? 'pharmacy Name' }}</div>
            <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
                {{ $pharmacy['address'] ?? '' }}<br>
                {{ $pharmacy['phone'] ?? '' }}<br>
                {{ ($pharmacy['email'] ?? '') . ' | ' . ($pharmacy['website'] ?? '') }}
            </div><br>
            <div>
                <h3 style="font-weight: bold; margin-top: -1%">Production Report</h3>
                <h4 style="margin-top: 0.4%">From: <b>{{ $pharmacy['from_date'] }}</b> To:
                    <b>{{ $pharmacy['to_date'] }}</b></h4>
                <h4 style="margin-top: 0%">Printed On: {{ now()->format('Y-m-d H:i:s') }}</h4>
            </div>
        </div>
        <div class="row">
            <div class="row" style="margin-top: 2%;">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Date</th>
                            <th align="center">Cows Received</th>
                            <th align="center">Live Weight (kg)</th>
                            <th align="center">Meat Output (kg)</th>
                            <th align="center">Yield (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $prod)
                            <tr>
                                <td align="center">{{ $loop->iteration }}.</td>
                                <td align="">{{ $prod->production_date }}</td>
                                <td align="center">{{ $prod->cows_received }}</td>
                                <td align="center">{{ number_format($prod->total_weight, 2) }}</td>
                                <td align="center">{{ number_format($prod->meat_output, 2) }}</td>
                                <td align="center">
                                    {{ $prod->total_weight > 0 ? number_format(($prod->meat_output / $prod->total_weight) * 100, 2) : '0.00' }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No production records found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
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
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
    </script>
</body>

</html>