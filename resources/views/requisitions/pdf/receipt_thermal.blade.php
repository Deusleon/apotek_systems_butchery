<!DOCTYPE html>
<html>
<head>
    <title>Requisition</title>
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
        }

        .req-info td {
            padding: 4px 6px;
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
    </style>
</head>
<body>
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
            @if(!empty($pharmacy['tin_number']))
                TIN: {{ $pharmacy['tin_number'] }}<br>
            @endif
            {{ $pharmacy['email'] . ' | ' . $pharmacy['website'] }}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Stock Requisition</h3>
            <h4 align="center" style="margin-top: -1%">Date: <b>{{ date('Y-m-d', strtotime($requisition->created_at)) }}</b></h4>
            <h4 align="center" style="margin-top: -1%">From: <b>{{ $fromStore->name ?? '' }}</b> To: <b>{{ $toStore->name ?? '' }}</b></h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{ date('Y-m-d H:i:s') }}</h4>
        </div>
    </div>

    <!-- Requisition Info -->
    <table class="req-info" align="center">
        <tr>
            <td><b>Requisition #:</b> {{ $requisition->req_no ?? '' }}</td>
        </tr>
    </table>

    <!-- Requisition Items -->
    <div>
        <table id="items" align="center" style="margin-top: -43px;">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 60%">Product Name</th>
                    <th style="width: 35%; text-align: center;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requisitionDet as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $item->products_->name ?? '' }}
                            @if(!empty($item->products_->brand)) {{ $item->products_->brand }} @endif
                            @if(!empty($item->products_->pack_size) && !empty($item->products_->sales_uom))
                                {{ $item->products_->pack_size }}{{ $item->products_->sales_uom }}
                            @endif
                        </td>
                        <td style="text-align: center;">{{ number_format($item->quantity, 0) ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Created By: <b>{{ $requisition->creator->name }}</b></p>
        @if(!empty($pharmacy['slogan']))
            <p class="slogan">{{ $pharmacy['slogan'] }}</p>
        @endif
    </div>

    <!-- Page Numbering -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 280;
            $y = 820;
            $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
            $font = null;
            $size = 10;
            $color = [0,0,0];
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>