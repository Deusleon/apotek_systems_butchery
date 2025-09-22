<!DOCTYPE html>
<html>

<head>
    <title>Inventory Count Sheet</title>


    <style>
        body {
            font-size: 14px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th,
        td {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            padding: 10px;
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
            /*border-spacing: 5px;*/
            width: 100%;
            margin-bottom: 10%;
        }

        #table-top-detail {
            /*border-spacing: 5px;*/
            width: 100%;
            margin-top: -10%;
            margin-bottom: -3%;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #table-info {
            width: 50%;
            border-spacing: 5px;
        }

        .tab {
            display: inline-block;
            margin-left: 20px;
        }

        #receiver-sign {
            top: 10%;
        }

        #sender-sign {
            margin-top: 0%;
        }

        .topcorner {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: -4%;
            margin-left: 78%;
        }

        .topcorner>p {
            font-size: 10px;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }
    </style>


</head>

<body>
    {{-- @dd($data) --}}
    <h2 align="center">{{$pharmacy['name']}}</h2>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
    <h2 align="center" style="margin-top: -2%">Inventory Count Sheet</h2>

    <div class="row" style="margin-top: -2%">
        <div class="col-md-12">
            <div align="center">Perfomed By: {{Auth::user()->name}}, on {{date('d-m-Y')}}</div>
            <div align="center">Branch: <b>{{ $default_store }}</b></div>
            @php
                $groupedData = [];
                foreach ($data as $store => $stocks) {
                    foreach ($stocks as $stock) {
                        $pid = $stock['product_id'];

                        if (!isset($groupedData[$pid])) {
                            $groupedData[$pid] = $stock;
                        } else {
                            $groupedData[$pid]['quantity_on_hand'] += $stock['quantity_on_hand'];
                        }
                    }
                }
                $groupedData = array_values($groupedData);
            @endphp

            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white; font-size: 0.9em">
                        <th align="left">#</th>
                        <th align="left">Product Name</th>
                        @if ($showQoH)
                            <th align="center">QOH</th>
                            <th align="center">Physical</th>
                        @else
                            <th align="center">Qty</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedData as $stock)
                        <tr>
                            <td align="left">{{ $loop->iteration }}.</td>
                            <td align="left">{{ $stock['product_name'] . ' ' . $stock['brand'] . ' ' . $stock['pack_size'] . $stock['sales_uom'] }}
                            </td>
                        @if ($showQoH)
                            <td align="center">{{ number_format($stock['quantity_on_hand']) }}</td>
                            <td></td>
                        @else
                            <td></td>
                        @endif
                        </tr>
                    @endforeach
                </tbody>
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