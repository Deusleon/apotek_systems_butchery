<!DOCTYPE html>
<html>
<head>
    <title>Requisition Issue History - {{ $requisition->req_no }}</title>
    <style>
        * {
            font-family: Verdana, Arial, sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            line-height: 1.4;
            font-size: 12px;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 8px;
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
            margin-top: 15px;
        }

        #items tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #items th {
            background: #1f273b;
            color: white;
            text-align: left;
            padding: 10px 8px;
            font-weight: bold;
        }

        #items td {
            padding: 8px;
        }

        h1, h2, h3, h4 {
            font-weight: normal;
            margin: 5px 0;
            line-height: 1.2;
        }

        #container .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        #container .logo-container img {
            max-width: 120px;
            max-height: 120px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .slogan {
            font-style: italic;
            margin-top: 10px;
            color: #666;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }

        /* Updated first table styling to match reference design */
        .info-table {
            width: 100%;
            margin-top: -15px
            border-collapse: collapse;
        }

        .info-table td {
            padding: 5px;
            border: 1px solid #858484;
            font-size: 11px;
            vertical-align: top;
        }

        .col-25 { 
            width: 25%; 
        }

        .header-section {
            margin-bottom: 25px;
        }

        .content-section {
            margin-top: -15px;
        }

        /* Page break protection */
        .page-break-protect {
            page-break-inside: avoid;
        }

        /* Ensure proper spacing */
        .spacing {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <div id="container" class="page-break-protect">
        <div class="logo-container">
            @if($pharmacy['logo'])
                <img src="{{ public_path('fileStore/logo/'.$pharmacy['logo']) }}" />
            @endif
        </div>
    </div>

    <!-- Pharmacy Information -->
    <div class="header-section page-break-protect">
        <h1 align="center" style="font-size: 18px; margin-bottom: 10px;">{{ $pharmacy['name'] }}</h1>
        <h3 align="center" style="margin: 5px 0;">{{ $pharmacy['address'] }}</h3>
        <h3 align="center" style="margin: 5px 0;">{{ $pharmacy['phone'] }}</h3>
        @if(!empty($pharmacy['tin_number']))
            <h3 align="center" style="margin: 5px 0;">TIN: {{ $pharmacy['tin_number'] }}</h3>
        @endif
        <h2 align="center" style="margin: 15px 0; font-size: 16px; font-weight: bold;">
            STOCK ISSUE HISTORY
        </h2>
    </div>

    <!-- Requisition Info - UPDATED TO MATCH REFERENCE DESIGN -->
    <div class="content-section page-break-protect">
        <table class="info-table">
            <tbody>
                <tr>
                    <td class="col-25"><strong>Requisition #</strong></td>
                    <td class="col-25"><strong>Date</strong></td>
                    <td class="col-25"><strong>From</strong></td>
                    <td class="col-25"><strong>To</strong></td>
                </tr>
                <tr>
                    <td class="col-25">{{ $requisition->req_no }}</td>
                    <td class="col-25">{{ date('Y-m-d', strtotime($requisition->created_at)) }}</td>
                    <td class="col-25">{{ $fromStore->name ?? '' }}</td>
                    <td class="col-25">{{ $toStore->name ?? '' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Requisition Items -->
    <div class="content-section">
        <table id="items">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 55%">Product Name</th>
                    <th style="width: 15%" class="text-center">Requested</th>
                    <th style="width: 15%" class="text-center">Issued</th>
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

    @if(!empty($requisition->remarks))
    <div class="content-section" style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd;">
        <p style="margin: 0;"><b>Remarks:</b> {{ $requisition->remarks }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer page-break-protect">
        <p style="margin: 5px 0;">Issued By: <b>{{ $requisition->creator->name ?? 'N/A' }}</b></p>
        <!-- <p style="margin: 5px 0;">Printed on: <b>{{ date('Y-m-d H:i:s') }}</b></p> -->
        @if(!empty($pharmacy['slogan']))
            <p class="slogan" style="margin: 10px 0;">{{ $pharmacy['slogan'] }}</p>
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
</html>