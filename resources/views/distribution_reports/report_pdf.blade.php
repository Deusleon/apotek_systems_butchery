<!DOCTYPE html>
<html>

<head>
    <title>Distribution Report</title>
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
            padding-left: 5px;
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

        .table-detail {
            width: 100%;
            margin-bottom: 15px;
        }

        .table-detail tr:nth-child(even) {
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

        .date-header {
            margin-left: 5px;
            font-weight: bold;
            font-size: 13px;
            margin-top: 15px;
            margin-bottom: 0;
        }

        .date-section {
            margin-bottom: 20px;
        }

        .grand-total-section {
            margin-left: 5px;
            border-top: 2px solid #1f273b;
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
            <div style="font-weight: bold; font-size: 16px;">{{ $pharmacy['name'] ?? 'Company Name' }}</div>
            <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
                {{ $pharmacy['address'] ?? '' }}<br>
                {{ $pharmacy['phone'] ?? '' }}<br>
                {{ ($pharmacy['email'] ?? '') . ' | ' . ($pharmacy['website'] ?? '') }}
            </div><br>
            <div>
                <h3 style="font-weight: bold; margin-top: -1%">Distribution Report</h3>
                @if(isset($selectedStore) && $selectedStore)
                    <h4 style="margin-top: 0.4%">Branch: <b>{{ $selectedStore->name }}</b></h4>
                @else
                    <h4 style="margin-top: 0.4%">All Branches</h4>
                @endif
                <h4 style="margin-top: 0.4%">From: <b>{{ $pharmacy['from_date'] }}</b> To:
                    <b>{{ $pharmacy['to_date'] }}</b></h4>
                <h4 style="margin-top: 0%">Printed On: {{ now()->format('Y-m-d H:i:s') }}</h4>
            </div>
        </div>

        @php
            // Helper function to format numbers with decimals only when applicable
            function formatSmartDecimal($num) {
                if ($num === null || $num === '' || $num == 0) {
                    return '-';
                }
                $num = floatval($num);
                if ($num == floor($num)) {
                    return number_format($num, 0);
                }
                return rtrim(rtrim(number_format($num, 2), '0'), '.');
            }
        @endphp

        {{-- Tables grouped by date --}}
        @foreach($data['dateGroups'] as $date => $dateData)
            <div class="date-section">
                <div class="date-header">
                    {{ date('Y-m-d', strtotime($date)) }}
                </div>
                <table class="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            {{-- <th align="left">Type</th> --}}
                            <th align="left">Items</th>
                            <th align="center">Meat (kg)</th>
                            <th align="center">Steak (kg)</th>
                            <th align="center">Beef Fillet (kg)</th>
                            <th align="center">Beef Liver (kg)</th>
                            <th align="center">Tripe (kg)</th>
                            {{-- <th align="center">Total (kg)</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dateData['rows'] as $index => $row)
                            @php
                                $rowTotal = $row['meat'] + $row['steak'] + $row['beef_fillet'] + $row['beef_liver'] + $row['tripe'];
                                $typeLabel = ($row['distribution_type'] ?? 'branch') === 'branch' ? 'Branch' : 
                                            (($row['distribution_type'] ?? '') === 'cash_sale' ? 'Cash Sale' : 'Order');
                            @endphp
                            <tr>
                                <td align="center">{{ $index + 1 }}.</td>
                                {{-- <td align="left">{{ $typeLabel }}</td> --}}
                                <td align="left">{{ $row['recipient_name'] ?? $row['store_name'] ?? 'Unknown' }}</td>
                                <td align="center">{{ formatSmartDecimal($row['meat']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['steak']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['beef_fillet']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['beef_liver']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['tripe']) }}</td>
                                {{-- <td align="center"><b>{{ formatSmartDecimal($rowTotal) }}</b></td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: #e9ecef; font-weight: bold;">
                            <td colspan="2" align="right"><b>Day Total:</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($dateData['totals']['meat']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($dateData['totals']['steak']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($dateData['totals']['beef_fillet']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($dateData['totals']['beef_liver']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($dateData['totals']['tripe']) }}</b></td>
                            @php
                                $dayTotal = $dateData['totals']['meat'] + $dateData['totals']['steak'] + $dateData['totals']['beef_fillet'] + $dateData['totals']['beef_liver'] + $dateData['totals']['tripe'];
                            @endphp
                            {{-- <td align="center"><b>{{ formatSmartDecimal($dayTotal) }}</b></td> --}}
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach

        {{-- Grand Total Section --}}
        {{-- <div class="grand-total-section">
            <table class="table-detail" align="center">
                <tbody>
                    <tr>
                        <th align="right">GRAND TOTALS</th>
                        <td align="center"><b>{{ formatSmartDecimal($data['totals']['meat']) }}</b></td>
                        <td align="center"><b>{{ formatSmartDecimal($data['totals']['steak']) }}</b></td>
                        <td align="center"><b>{{ formatSmartDecimal($data['totals']['beef_fillet']) }}</b></td>
                        <td align="center"><b>{{ formatSmartDecimal($data['totals']['beef_liver']) }}</b></td>
                        <td align="center"><b>{{ formatSmartDecimal($data['totals']['tripe']) }}</b></td>
                        @php
                            $grandTotal = $data['totals']['meat'] + $data['totals']['steak'] + $data['totals']['beef_fillet'] + $data['totals']['beef_liver'] + $data['totals']['tripe'];
                        @endphp
                    </tr>
                </tbody>
            </table>
        </div> --}}

    </div>
</body>

</html>
