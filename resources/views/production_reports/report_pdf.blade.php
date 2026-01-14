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
                            <th align="left">Details</th>
                            <th align="center">Count</th>
                            <th align="center">Total Weight</th>
                            <th align="center">Wt. Diff</th>
                            <th align="center">Meat</th>
                            <th align="center">Steak</th>
                            <th align="center">Beef Fillet</th>
                            <th align="center">Beef Liver</th>
                            <th align="center">Tripe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCows = 0;
                            $totalWeight = 0;
                            $totalMeat = 0;
                            $totalSteak = 0;
                            $totalBeefFillet = 0;
                            $totalWtDiff = 0;
                            $totalBeefLiver = 0;
                            $totalTripe = 0;
                            
                            // Helper function to format numbers with decimals only when applicable
                            function formatSmartDecimal($num) {
                                if ($num == floor($num)) {
                                    return number_format($num, 0);
                                }
                                return rtrim(rtrim(number_format($num, 2), '0'), '.');
                            }
                        @endphp
                        @forelse($data as $prod)
                            @php
                                $totalCows += $prod->items_received;
                                $totalWeight += $prod->total_weight;
                                $totalMeat += $prod->meat;
                                $totalSteak += $prod->steak;
                                $totalBeefFillet += $prod->beef_fillet;
                                $totalWtDiff += $prod->weight_difference;
                                $totalBeefLiver += $prod->beef_liver;
                                $totalTripe += $prod->tripe ?? 0;
                            @endphp
                            <tr>
                                <td align="center">{{ $loop->iteration }}.</td>
                                <td align="left">{{ $prod->production_date }}</td>
                                <td align="left">{{ $prod->details ?? '-' }}</td>
                                <td align="center">{{ number_format($prod->items_received) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->total_weight) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->weight_difference) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->meat) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->steak) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->beef_fillet) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->beef_liver) }}</td>
                                <td align="center">{{ formatSmartDecimal($prod->tripe ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">No production records found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr style="background: #e9ecef; font-weight: bold;">
                            <td colspan="3" align="right"><strong>TOTALS:</strong></td>
                            <td align="center">{{ number_format($totalCows) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalWeight) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalWtDiff) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalMeat) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalSteak) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalBeefFillet) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalBeefLiver) }}</td>
                            <td align="center">{{ formatSmartDecimal($totalTripe) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Summary Section -->
            @php
                $meatPrice = isset($prices['meat']) ? floatval($prices['meat']) : 0;
                $steakPrice = isset($prices['steak']) ? floatval($prices['steak']) : 0;
                $beefFilletPrice = isset($prices['beef_fillet']) ? floatval($prices['beef_fillet']) : 0;
                $beefLiverPrice = isset($prices['beef_liver']) ? floatval($prices['beef_liver']) : 0;
                $tripePrice = isset($prices['tripe']) ? floatval($prices['tripe']) : 0;

                $meatTotal = $totalMeat * $meatPrice;
                $steakTotal = $totalSteak * $steakPrice;
                $beefFilletTotal = $totalBeefFillet * $beefFilletPrice;
                $beefLiverTotal = $totalBeefLiver * $beefLiverPrice;
                $tripeTotal = $totalTripe * $tripePrice;

                $grandTotal = $meatTotal + $steakTotal + $beefFilletTotal + $beefLiverTotal + $tripeTotal;
                
                $hasPrices = $meatPrice > 0 || $steakPrice > 0 || $beefFilletPrice > 0 || $beefLiverPrice > 0 || $tripePrice > 0;
            @endphp

            <div style="margin-top: 20px; page-break-inside: avoid;">
                <h3 style="font-weight: bold; text-align: left; margin-bottom: 10px; border-bottom: 2px solid #1f273b; padding-bottom: 5px;">SUMMARY</h3>
                
                <table style="width: 22%; margin-left: 0;">
                    <tr>
                        <td style="padding: 5px 0;"><strong>Total Items (Cows):</strong></td>
                        <td style="padding: 5px 0; margin-left: 100px;" align="left">{{ number_format($totalCows) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Total Weight:</strong></td>
                        <td style="padding: 5px 0; margin-left: 100px;" align="left">{{ formatSmartDecimal($totalWeight) }} kg</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0;"><strong>Total Weight Difference:</strong></td>
                        <td style="padding: 5px 0; margin-left: 100px;" align="left">{{ formatSmartDecimal($totalWtDiff) }} kg</td>
                    </tr>
                </table>

                <h4 style="font-weight: bold; text-align: left; margin-top: 15px; margin-bottom: 10px;">Meat Type Breakdown:</h4>
                
                <table style="width: 60%; margin-left: 0; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #1f273b; color: #ffffff; border-bottom: 1px solid #dee2e6;">
                            <th align="left" style="padding: 8px;">Meat Type</th>
                            <th align="left" style="padding: 8px;"></th>
                            <th align="center" style="padding: 8px;">Weight (kg)</th>
                            @if($hasPrices)
                            <th align="center" style="padding: 8px;"></th>
                            <th align="left" style="padding: 8px;">Price/kg</th>
                            <th align="center" style="padding: 8px;"></th>
                            <th align="right" style="padding: 8px;">Total</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 8px 0px;"><strong>i.</strong> Meat</td>
                            <td style="padding: 8px;"> :</td>
                            <td align="center" style="padding: 8px;">{{ formatSmartDecimal($totalMeat) }}</td>
                            @if($hasPrices)
                            <td align="center" style="padding: 8px;">×</td>
                            <td align="left" style="padding: 8px;">{{ number_format($meatPrice, 2) }}</td>
                            <td align="center" style="padding: 8px;">=</td>
                            <td align="right" style="padding: 8px;"><strong>{{ number_format($meatTotal, 2) }}</strong></td>
                            @endif
                        </tr>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 8px 0px;"><strong>ii.</strong> Steak</td>
                            <td style="padding: 8px;"> :</td>
                            <td align="center" style="padding: 8px;">{{ formatSmartDecimal($totalSteak) }}</td>
                            @if($hasPrices)
                            <td align="center" style="padding: 8px;">×</td>
                            <td align="left" style="padding: 8px;">{{ number_format($steakPrice, 2) }}</td>
                            <td align="center" style="padding: 8px;">=</td>
                            <td align="right" style="padding: 8px;"><strong>{{ number_format($steakTotal, 2) }}</strong></td>
                            @endif
                        </tr>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 8px 0px;"><strong>iii.</strong> Beef Fillet</td>
                            <td style="padding: 8px;"> :</td>
                            <td align="center" style="padding: 8px;">{{ formatSmartDecimal($totalBeefFillet) }}</td>
                            @if($hasPrices)
                            <td align="center" style="padding: 8px;">×</td>
                            <td align="left" style="padding: 8px;">{{ number_format($beefFilletPrice, 2) }}</td>
                            <td align="center" style="padding: 8px;">=</td>
                            <td align="right" style="padding: 8px;"><strong>{{ number_format($beefFilletTotal, 2) }}</strong></td>
                            @endif
                        </tr>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 8px 0px;"><strong>iv.</strong> Beef Liver</td>
                            <td style="padding: 8px;"> :</td>
                            <td align="center" style="padding: 8px;">{{ formatSmartDecimal($totalBeefLiver) }}</td>
                            @if($hasPrices)
                            <td align="center" style="padding: 8px;">×</td>
                            <td align="left" style="padding: 8px;">{{ number_format($beefLiverPrice, 2) }}</td>
                            <td align="center" style="padding: 8px;">=</td>
                            <td align="right" style="padding: 8px;"><strong>{{ number_format($beefLiverTotal, 2) }}</strong></td>
                            @endif
                        </tr>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 8px 0px;"><strong>v.</strong> Tripe</td>
                            <td style="padding: 8px;"> :</td>
                            <td align="center" style="padding: 8px;">{{ formatSmartDecimal($totalTripe) }}</td>
                            @if($hasPrices)
                            <td align="center" style="padding: 8px;">×</td>
                            <td align="left" style="padding: 8px;">{{ number_format($tripePrice, 2) }}</td>
                            <td align="center" style="padding: 8px;">=</td>
                            <td align="right" style="padding: 8px;"><strong>{{ number_format($tripeTotal, 2) }}</strong></td>
                            @endif
                        </tr>
                    </tbody>
                    @if($hasPrices)
                    <tfoot>
                        <tr style="">
                            <td colspan="5" align="right" style="padding: 10px;"><strong>GRAND TOTAL</strong></td>
                            <td align="center" style="padding: 8px;"> =</td>
                            <td align="right" style="padding: 10px;"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                    @endif
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