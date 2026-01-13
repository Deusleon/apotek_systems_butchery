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

        .date-row {
            background-color: #e9ecef !important;
            font-weight: bold;
        }

        .date-row td {
            border-top: 2px solid #dee2e6;
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
        <div class="row">
            <div class="row" style="margin-top: 2%;">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Date</th>
                            <th align="left">Branch</th>
                            <th align="center">Meat (kg)</th>
                            <th align="center">Steak (kg)</th>
                            <th align="center">Beef Fillet (kg)</th>
                            <th align="center">Beef Liver (kg)</th>
                            <th align="center">Tripe (kg)</th>
                            <th align="center">Total (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            
                            $currentDate = null;
                            $counter = 0;
                        @endphp
                        @forelse($data['rows'] as $row)
                            @php
                                $counter++;
                                $rowTotal = $row['meat'] + $row['steak'] + $row['beef_fillet'] + $row['beef_liver'] + $row['tripe'];
                            @endphp
                            <tr>
                                <td align="center">{{ $counter }}.</td>
                                <td align="left">{{ $row['date'] }}</td>
                                <td align="left">{{ $row['store_name'] }}</td>
                                <td align="center">{{ formatSmartDecimal($row['meat']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['steak']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['beef_fillet']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['beef_liver']) }}</td>
                                <td align="center">{{ formatSmartDecimal($row['tripe']) }}</td>
                                <td align="center"><b>{{ formatSmartDecimal($rowTotal) }}</b></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" align="center">No distribution records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(!empty($data['rows']))
                    <tfoot>
                        <tr style="background: #dee2e6; font-weight: bold;">
                            <td colspan="3" align="right"><b>TOTALS:</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($data['totals']['meat']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($data['totals']['steak']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($data['totals']['beef_fillet']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($data['totals']['beef_liver']) }}</b></td>
                            <td align="center"><b>{{ formatSmartDecimal($data['totals']['tripe']) }}</b></td>
                            @php
                                $grandTotal = $data['totals']['meat'] + $data['totals']['steak'] + $data['totals']['beef_fillet'] + $data['totals']['beef_liver'] + $data['totals']['tripe'];
                            @endphp
                            <td align="center"><b>{{ formatSmartDecimal($grandTotal) }}</b></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Summary by Branch --}}
        @if(!isset($selectedStore) || !$selectedStore)
        <div class="row" style="margin-top: 20px;">
            <h4 style="font-weight: bold; text-align: left; margin-bottom: 10px;">Summary by Branch</h4>
            <table id="table-summary" style="width: 100%;" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="left">Branch</th>
                        <th align="center">Meat (kg)</th>
                        <th align="center">Steak (kg)</th>
                        <th align="center">Beef Fillet (kg)</th>
                        <th align="center">Beef Liver (kg)</th>
                        <th align="center">Tripe (kg)</th>
                        <th align="center">Total (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Group totals by branch
                        $branchTotals = [];
                        foreach($data['rows'] as $row) {
                            $storeName = $row['store_name'];
                            if (!isset($branchTotals[$storeName])) {
                                $branchTotals[$storeName] = [
                                    'meat' => 0,
                                    'steak' => 0,
                                    'beef_fillet' => 0,
                                    'beef_liver' => 0,
                                    'tripe' => 0,
                                ];
                            }
                            $branchTotals[$storeName]['meat'] += $row['meat'];
                            $branchTotals[$storeName]['steak'] += $row['steak'];
                            $branchTotals[$storeName]['beef_fillet'] += $row['beef_fillet'];
                            $branchTotals[$storeName]['beef_liver'] += $row['beef_liver'];
                            $branchTotals[$storeName]['tripe'] += $row['tripe'];
                        }
                        ksort($branchTotals);
                    @endphp
                    @foreach($branchTotals as $branchName => $totals)
                        @php
                            $branchTotal = $totals['meat'] + $totals['steak'] + $totals['beef_fillet'] + $totals['beef_liver'] + $totals['tripe'];
                        @endphp
                        <tr>
                            <td align="left">{{ $branchName }}</td>
                            <td align="center">{{ formatSmartDecimal($totals['meat']) }}</td>
                            <td align="center">{{ formatSmartDecimal($totals['steak']) }}</td>
                            <td align="center">{{ formatSmartDecimal($totals['beef_fillet']) }}</td>
                            <td align="center">{{ formatSmartDecimal($totals['beef_liver']) }}</td>
                            <td align="center">{{ formatSmartDecimal($totals['tripe']) }}</td>
                            <td align="center"><b>{{ formatSmartDecimal($branchTotal) }}</b></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</body>

</html>
