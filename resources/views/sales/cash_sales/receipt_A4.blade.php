<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            /* border-left: 2px dashed #949393; */
            /* min-height: 135mm; */
        }

        .header-section {
            display: flex;
            align-items: center;
            /* margin-bottom: 10px; */
        }

        .logo-container {
            margin-right: 10px;
        }

        .logo-container img {
            max-width: 100px;
            max-height: 100px;
        }

        .company-info {
            flex: 1;
            margin-left: 110px;
        }

        .company-name {
            font-weight: bold;
            font-size: 18px;
        }

        .company-address {
            font-size: 14px;
            line-height: 1.2;
        }

        .receipt-header {
            text-align: right;
            margin-top: -5%;
        }

        .receipt-title {
            font-weight: bold;
            font-size: 17px;
            margin: 0;
        }

        .receipt-number {
            font-weight: bold;
            font-size: 12px;
            margin: 2px 0;
            color: red;
        }

        .bill-to-label {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .bill-to-line1 {
            border-bottom: 1.5px solid #a19f9f;
            width: 40%;
            height: 15px;
            margin-top: -25px;
        }

        .bill-to-line2 {
            border-bottom: 1.5px solid #a19f9f;
            width: 40%;
            height: 15px;
            margin-top: -18px;
        }

        .date-section {
            float: right;
            display: inline-flex;
            text-align: left;
        }

        .date-label {
            font-size: 12px;
            font-weight: bold;
            margin-top: 41px;
        }

        .date-line {
            margin-left: 30px;
            margin-top: 50px;
            border-bottom: 1.5px solid #a19f9f;
            padding-left: 8px;
            padding-right: 30px;
            padding-top: 40px;
            font-size: 13px;
            height: 15px;
        }

        /* Table styling */
        .items-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .table-header {
            background-color: #000;
            color: white;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
        }

        .table-header th {
            padding: 8px 2px;
            border: 1px solid #dbdada;
        }

        .items-table td {
            padding: 8px 2px;
            border: 1px solid #858484;
            font-size: 14px;
            height: 15px;
        }

        .index-col {
            width: auto;
            text-align: center;
        }

        .description-col {
            width: 45%;
        }

        .qty-col {
            width: 10.33%;
            text-align: center;
        }

        .unit-col,
        .amount-col {
            width: 20.33%;
            text-align: right;
        }

        /* Summary section */
        .summary-section {
            width: 40%;
            margin-left: auto;
            font-size: 13px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            /* margin-bottom: 3px; */
            padding: 2px 5px;
        }


        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            background-color: #f0f0f0;
        }

        .sold-by {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-top: -40px;
        }

        .slogan {
            text-align: left;
            font-size: 10px;
            font-style: italic;
        }

        /* Customer info inline */
        .customer-info {
            padding-left: 12mm;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .info-left,
        .info-right {
            display: flex;
            align-items: center;
        }

        .info-left2 {
            margin-top: 10px;
        }

        .info-right {
            margin-right: 20px;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section">
        @if($pharmacy['logo'])
            <div class="logo-container">
                <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
            </div>
        @endif
        <div class="company-info">
            <div class="company-name">{{$pharmacy['name']}}</div>
            <div class="company-address">
                {{$pharmacy['address']}}<br>
                {{$pharmacy['phone']}}<br>
                <span>TIN: {{$pharmacy['tin_number']}}</span> |
                <span>VRN: {{$pharmacy['vrn_number']}}</span>
            </div>
        </div>
    </div>

    <!-- Receipt Header -->
    <div class="receipt-header">
        <div class="receipt-title">RECEIPT</div>
        @foreach($data as $datas => $dat)
            <span style="font-size: 12px; font-weight: 400;">NO.</span><span class="receipt-number"> {{$datas}}</span>
            @break
        @endforeach
    </div>
    <div style="display: inline-flex;">
        {{-- @dd($data) --}}
        @foreach($data as $datas => $dat)
            <!-- Bill To Section -->
            <div class="bill-to-section">
                <div class="bill-to-label">BILL TO:</div>
                <div class="info-left">
                    {{$dat[0]['customer'] ?? 'CASH'}}
                </div>
                <div class="bill-to-line1"></div>
                <div class="info-left2">
                    TIN: {{$dat[0]['customer_tin'] ?? 'N/A'}}
                </div>
                <div class="bill-to-line2"></div>
            </div>

            <!-- Date Section -->
            <div class="date-section">
                <div class="date-label">DATE:</div>
                <div class="date-line">{{date('Y-m-d', strtotime($dat[0]['created_at']))}}</div>
            </div>
        @endforeach
    </div>

    <!-- Customer Information -->
    @foreach($data as $datas => $dat)
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr class="table-header">
                    <th class="index-col">#</th>
                    <th class="description-col">DESCRIPTION</th>
                    <th class="qty-col">QTY</th>
                    <th class="unit-col">UNIT PRICE</th>
                    <th class="amount-col">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dat as $item)
                    <tr>
                        <td class="index-col">{{$loop->iteration}}.</td>
                        <td class="description-col">{{$item['name']}}</td>
                        <td class="qty-col">{{number_format($item['quantity'], 0)}}</td>
                        <td class="unit-col">{{number_format($item['price'] / $item['quantity'], 2)}}</td>
                        <td class="amount-col">{{number_format($item['price'] * $item['quantity'], 2)}}</td>
                    </tr>
                @endforeach

                @if(count($dat) < 5)
                    <!-- Empty rows for spacing -->
                    @for($i = 0; $i < 7 - count($dat); $i++)
                        <tr>
                            <td class="index-col"></td>
                            <td class="description-col">&nbsp;</td>
                            <td class="qty-col">&nbsp;</td>
                            <td class="unit-col">&nbsp;</td>
                            <td class="amount-col">&nbsp;</td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section" style="margin-top: 5px;">
            <div class="summary-row">
                <div>SUB TOTAL:</div>
                <div style="float: right;">
                    {{number_format(($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total']), 2)}}
                </div>
            </div>
            @if($dat[0]['discount_total'] > 0)
                <div class="summary-row">
                    <div>DISCOUNT:</div>
                    <div style="float: right;">{{number_format($dat[0]['discount_total'], 2)}}</div>
                </div>
            @endif
            <div class="summary-row">
                <span>VAT:</span>
                <span style="float: right;">{{number_format($dat[0]['total_vat'], 2)}}</span>
            </div>
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span style="float: right;">{{number_format($dat[0]['grand_total'], 2)}}</span>
            </div>

            @if($page == -1)
                <div class="summary-row" style="margin-top: 10px;">
                    <span>PAID:</span>
                    <span style="float: right;">{{number_format($dat[0]['paid'], 2)}}</span>
                </div>
                <div class="summary-row">
                    <span>BALANCE:</span>
                    <span style="float: right;">{{number_format($dat[0]['grand_total'] - $dat[0]['paid'], 2)}}</span>
                </div>
            @endif
        </div>

        @break
    @endforeach

    <!-- Footer Section -->
    <div class="footer-section">
        @foreach($data as $datas => $dat)
            <div class="sold-by">Issued By: {{$dat[0]['sold_by']}}</div>
            <div class="slogan">{{$pharmacy['slogan'] ?? 'Thank you for your business'}}</div>
            @break
        @endforeach
    </div>

</body>

</html>