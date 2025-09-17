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
            Requisition
        </h2>
        <br>
        <h4 align="center" style="margin-top: -1%">{{ date('j M, Y', strtotime($requisition->created_at)) }}</h4>
    </div>

    <!-- Requisition Info -->
    <table class="req-info" align="center">
        <tr>
            <td><b>Requisition #:</b> {{ $requisition->req_no ?? '' }}</td>
            <td><b>Created By:</b> {{ $requisition->creator->name }}</td>
        </tr>
    </table>

    <!-- Requisition Items -->
    <div style="margin-top: 2%;">
        <table id="items" align="center">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 70%">Product</th>
                    <th style="width: 25%">Quantity</th>
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
                        <td>{{ number_format($item->quantity, 0) ?? '' }}</td>
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
