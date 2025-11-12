<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
        }

        .receipt-header {
            text-align: right;
            margin-top: -5%;
        }

        .receipt-title {
            font-weight: bold;
            font-size: 15px;
            margin: 0;
        }

        /* Table styling */
        .customer-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .customer-table .index-col {
            width: auto;
            text-align: left;
            padding-left: 5px;
        }

        .customer-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 12px;
            height: 15px;
        }

        .items-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-header {
            background-color: #000;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }

        .table-header th {
            padding: 6px 2px;
            border: 1px solid #dbdada;
        }

        .items-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 12px;
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
            font-size: 12px;
        }

        hr {
            border: none;
            border-bottom: 1px solid #858484;
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

        .footer-note {
            text-align: left;
            font-size: 12px;
            width: 55%;
            text-align: justify;
            margin-bottom: 3px;
            margin-top: -90px;
        }

        .sold-by {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-top: 10px;
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
    <div style="width: 100%; text-align: center; align-items: center;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;" src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            <span>TIN: {{$pharmacy['tin_number'] ?? 'N/A'}}</span> |
            <span>VRN: {{$pharmacy['vrn_number'] ?? 'N/A'}}</span>
        </div>
    </div>
    <div style="font-weight: bold; text-align: center;">
        CREDIT INVOICE
    </div>
    @foreach($data as $datas => $dat)
        <table class="customer-table">
            <tbody>
                <tr>
                    <td class="index-col" style="width: 15%;">Customer Name:</td>
                    <td class="index-col" style="width: 50%;">{{$dat[0]['customer'] ?? 'CASH'}}</td>
                    <td class="index-col" style="width: 17%;">TIN:</td>
                    <td class="index-col" style="width: 21%;">{{$dat[0]['customer_tin'] ?? 'N/A'}}</td>
                </tr>
                <tr>
                    <td class="index-col" style="width: 15%;">Phone Number:</td>
                    <td class="index-col" style="width: 50%;">{{$dat[0]['customer_phone'] ?? 'N/A'}}</td>
                    <td class="index-col" style="width: 17%">Receipt No:</td>
                    <td class="index-col" style="width: 20%;">{{$datas ?? 'N/A'}}</td>
                </tr>
                <tr>
                    <td class="index-col" style="width: 15%;">Address:</td>
                    <td class="index-col" style="width: 50%;">{{$dat[0]['customer_address'] ?? 'N/A'}}</td>
                    <td class="index-col" style="width: 17%">Date:</td>
                    <td class="index-col" style="width: 20%;">{{date('Y-m-d', strtotime($dat[0]['created_at']))}}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    @foreach($data as $datas => $dat)
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr class="table-header">
                    <th class="index-col">#</th>
                    <th class="description-col">Description</th>
                    <th class="qty-col">Qty</th>
                    <th class="unit-col">Price</th>
                    <th class="amount-col">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dat as $item)
                    <tr>
                        <td class="index-col">{{$loop->iteration}}.</td>
                        <td class="description-col">{{$item['name']}} {{$item['brand'] ?? ''}}
                            {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}
                        </td>
                        <td class="qty-col">{{number_format($item['quantity'], 0)}}</td>
                        <td class="unit-col">{{number_format($item['price'], 2)}}</td>
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
        <div class="summary-section" style="margin-top: 15px;">
            <div class="summary-row">
                <div>Sub Total:</div>
                <div style="float: right;">
                    {{number_format(($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total']), 2)}}
                </div>
            </div>
            <div class="summary-row">
                <span>VAT:</span>
                <span style="float: right;">{{number_format($dat[0]['total_vat'], 2)}}</span>
            </div>
            @if($dat[0]['discount_total'] > 0)
                <div class="summary-row">
                    <div>Discount:</div>
                    <div style="float: right;">{{number_format($dat[0]['discount_total'], 2)}}</div>
                </div>
            @endif
            <div class="summary-row total">
                <span>Total:</span>
                <span style="float: right;">{{number_format($dat[0]['grand_total'], 2)}}</span>
            </div>
            <hr>
            @if($page == -1)
                <div class="summary-row">
                    <span>Paid:</span>
                    <span style="float: right;">{{number_format($dat[0]['paid'], 2)}}</span>
                </div>
                <div class="summary-row">
                    <span>Balance:</span>
                    <span style="float: right;">{{number_format($dat[0]['grand_total'] - $dat[0]['paid'], 2)}}</span>
                </div>
            @endif
        </div>

        @break
    @endforeach

    <!-- Footer Section -->
    <div class="footer-section" style="margin-top: -120px;">
        @foreach($data as $datas => $dat)
            <div class="sold-by">Issued By: {{$dat[0]['sold_by']}}</div>
            @break
        @endforeach
        <span style="font-size: 10px; border-bottom: 1px solid #ccc;">Printed on: {{date('Y-m-d H:i:s')}}</span>
    </div>

    @if($generalSettings && $generalSettings->credit_sale_terms)
        <div style="padding-top: 10px;">
            <div style="font-weight: bold; font-size: 12px; margin-bottom: 5px;">Terms & Conditions:</div>
            <div style="font-size: 10px; line-height: 1.4; text-align: justify;">
                {!! nl2br(e($generalSettings->credit_sale_terms)) !!}
            </div>
        </div>
    @endif
    <div
        style="width: 100%; text-align: center; font-size: 12px; margin-top: 100px !important; font-style: italic; display: flex; justify-content: center; align-items: center;">
        {{$pharmacy['slogan'] ?? 'Thank you for your business'}}
    </div>

</body>

</html>