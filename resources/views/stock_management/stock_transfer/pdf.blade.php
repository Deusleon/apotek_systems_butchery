<!DOCTYPE html>
<html>

<head>
    <title>Stock Transfer #{{ $transfer->transfer_no }}</title>

    <style>
        body {
            font-size: 12px;
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
        }

        #table-detail-main {
            width: 102%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
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
    <div class="row">
        <div id="container">
            <div class="logo-container">
                @if($pharmacy['logo'] ?? null)
                    <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
                @endif
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name'] ?? 'Company Name'}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address'] ?? ''}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone'] ?? ''}}</h3>
        <h3 align="center" style="margin-top: -1%">{{($pharmacy['email'] ?? '') . ' | ' . ($pharmacy['website'] ?? '')}}</h3>
        <h2 align="center" style="margin-top: -1%">Stock Transfer Receipt</h2>
        <h3 align="center" style="margin-top: -1%">Transfer No: <b>{{ $transfer->transfer_no }}</b></h3>
        <div class="row">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Product Name</th>
                            <th align="center">Quantity</th>
                            <th align="left">From Store</th>
                            <th align="left">To Store</th>
                            <th align="left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfer->all_items ?? [$transfer] as $index => $item)
                        <tr>
                            <td align="center">{{ $index + 1 }}.</td>
                            <td align="left">
                                {{ ($item->currentStock->product->name ?? '') . ' ' .
                                   ($item->currentStock->product->brand ?? '') . ' ' .
                                   ($item->currentStock->product->pack_size ?? '') . ' ' .
                                   ($item->currentStock->product->sales_uom ?? '') }}
                            </td>
                            <td align="center">{{ number_format($item->transfer_qty ?? 0) }}</td>
                            <td align="left">{{ $transfer->fromStore->name ?? '' }}</td>
                            <td align="left">{{ $transfer->toStore->name ?? '' }}</td>
                            <td align="left">
                                @php
                                    $statuses = [
                                        'created' => ['name' => 'Pending', 'class' => 'color: blue;'],
                                        'assigned' => ['name' => 'Assigned', 'class' => 'color: orange;'],
                                        'approved' => ['name' => 'Approved', 'class' => 'color: yellow;'],
                                        'in_transit' => ['name' => 'In Transit', 'class' => 'color: teal;'],
                                        'acknowledged' => ['name' => 'Acknowledged', 'class' => 'color: green;'],
                                        'completed' => ['name' => 'Completed', 'class' => 'color: green;'],
                                        'cancelled' => ['name' => 'Cancelled', 'class' => 'color: red;']
                                    ];
                                    $currentStatus = $transfer->status ?? 'created';
                                    $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'color: black;'];
                                @endphp
                                <span style="font-weight: bold; {{ $statusInfo['class'] }}">{{ $statusInfo['name'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($transfer->remarks)
                <div style="margin-top: 20px;">
                    <strong>Remarks:</strong> {{ $transfer->remarks }}
                </div>
                @endif

                <div style="margin-top: 20px;">
                    <table id="table-detail" align="center" style="width: 50%;">
                        <tr>
                            <td style="border: none; padding: 5px;"><strong>Created By:</strong></td>
                            <td style="border: none; padding: 5px;">{{ $transfer->created_by_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 5px;"><strong>Created Date:</strong></td>
                            <td style="border: none; padding: 5px;">{{ date('d-m-Y H:i:s', strtotime($transfer->created_at)) }}</td>
                        </tr>
                        {{-- @if($transfer->approved_by_name) --}}
                        <tr>
                            <td style="border: none; padding: 5px;"><strong>Approved By:</strong></td>
                            <td style="border: none; padding: 5px;">{{ $transfer->approved_by_name ?? 'N/A' }}</td>
                        </tr>
                        {{-- @endif --}}
                        {{-- @if($transfer->acknowledged_by_name) --}}
                        <tr>
                            <td style="border: none; padding: 5px;"><strong>Acknowledged By:</strong></td>
                            <td style="border: none; padding: 5px;">{{ $transfer->acknowledged_by_name ?? 'N/A' }}</td>
                        </tr>
                        {{-- @endif --}}
                    </table>
                </div>
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