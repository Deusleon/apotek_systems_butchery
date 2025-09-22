<!DOCTYPE html>
<html>
<head>
    <title>Requisition Issue History - {{ $requisition->req_no }}</title>
    <style>
        body {
            font-size: 12px;
            font-family: Verdana, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 10px;
        }

        table {
            width: 100%;
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

        #items {
            width: 100%;
            margin-top: 2%;
        }

        #items tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #items th {
            background: #1f273b;
            color: white;
            text-align: left;
        }

        h1, h2, h3, h4 {
            font-weight: normal;
            margin: 2px 0;
        }

        #container .logo-container {
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 160px;
            max-height: 160px;
        }

        .req-info {
            margin: 2% 0;
            font-size: 12px;
            width: 100%;
        }

        .req-info td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
        }

        .slogan {
            font-style: italic;
            margin-top: 5px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .difference-positive {
            color: green;
        }
        
        .difference-negative {
            color: red;
        }
        
        .difference-zero {
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <div id="container">
        <div class="logo-container">
            @if($pharmacy['logo'])
                <img src="{{ public_path('fileStore/logo/'.$pharmacy['logo']) }}" />
            @endif
        </div>
    </div>

    <!-- Pharmacy Information -->
    <div style="padding-top: 5px;">
        <h1 align="center">{{ $pharmacy['name'] }}</h1>
        <br>
        <h3 align="center" style="margin-top: -1%">{{ $pharmacy['address'] }}</h3>
        <br>
        <h3 align="center" style="margin-top: -1%">{{ $pharmacy['phone'] }}</h3>
        <br>
        @if(!empty($pharmacy['tin_number']))
            <h3 align="center" style="margin-top: -1%">TIN: {{ $pharmacy['tin_number'] }}</h3>
        @endif
        <br>
        <h2 align="center" style="margin-top: -1%; font-size: 20px; font-weight: bold;">
            REQUISITION ISSUE HISTORY
        </h2>
        <br>
        <h4 align="center" style="margin-top: -1%">{{ date('j M, Y', strtotime($requisition->updated_at)) }}</h4>
    </div>

    <!-- Requisition Info -->
    <table class="req-info">
        <tr>
            <td><b>Requisition #:</b> {{ $requisition->req_no ?? '' }}</td>
            <td><b>Issued By:</b> {{ $requisition->creator->name ?? '' }}</td>
        </tr>
        <tr>
            <td><b>From Store:</b> {{ $fromStore->name ?? '' }}</td>
            <td><b>To Store:</b> {{ $toStore->name ?? '' }}</td>
        </tr>
        @if(!empty($requisition->remarks))
        <tr>
            <td colspan="2"><b>Remarks:</b> {{ $requisition->remarks }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="2"><b>Date Issued:</b> {{ date('j M, Y H:i', strtotime($requisition->updated_at)) }}</td>
        </tr>
    </table>

    <!-- Requisition Items -->
    <div style="margin-top: 2%;">
        <table id="items">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 55%">Product</th>
                    <th style="width: 15%" class="text-center">Requested</th>
                    <th style="width: 15%" class="text-center">Given</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisitionDetails as $index => $item)
                    @php
                        $difference = $item->quantity_given - $item->quantity;
                        $diffClass = 'difference-zero';
                        $diffSymbol = '';
                        
                        if ($difference > 0) {
                            $diffClass = 'difference-positive';
                            $diffSymbol = '+';
                        } elseif ($difference < 0) {
                            $diffClass = 'difference-negative';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $item->products_->name ?? '' }}
                            @if(!empty($item->products_->brand)) {{ $item->products_->brand }} @endif
                            @if(!empty($item->products_->pack_size) && !empty($item->products_->sales_uom))
                                {{ $item->products_->pack_size }}{{ $item->products_->sales_uom }}
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-center">{{ number_format($item->quantity_given, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Issued By: <b>{{ $requisition->creator->name ?? 'N/A' }}</b></p>
        <p>Printed on: <b>{{ date('j M, Y H:i') }}</b></p>
        @if(!empty($pharmacy['slogan']))
            <p class="slogan">{{ $pharmacy['slogan'] }}</p>
        @endif
    </div>

    <!-- Page Numbering -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 280;
            $y = 820;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = null;
            $size = 10;
            $color = [0,0,0];
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>s