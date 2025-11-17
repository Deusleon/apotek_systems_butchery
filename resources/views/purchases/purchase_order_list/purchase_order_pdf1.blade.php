<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: -15px;
            margin-top: -25px;
            padding: 0;
            position: relative;
            min-height: 100vh;
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
        .supplier-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .supplier-table .index-col {
            width: auto;
            text-align: left;
            padding-left: 5px;
        }

        .supplier-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 13px;
            height: 15px;
        }

        .items-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 15px;
            margin-left: -1px;
            margin-right: -1px;
            margin-bottom: 10px;
        }

        .table-header {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .table-header th {
            padding: 4px 2px;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            border-left: 1px solid #858484;
            font-size: 13px;
        }

        .items-table td {
            padding: 4px 2px;
            border: 1px solid #858484;
            font-size: 13px;
            height: 15px;
        }

        /* Summary section */
        .summary-section {
            width: 40%;
            margin-left: auto;
            font-size: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 5px;
        }

        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            background-color: #f0f0f0;
        }

        .issued-by {
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .slogan-section {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            font-style: italic;
            padding: 10px 0;
            background-color: white;
            border-top: 1px solid #ccc;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;" src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">PURCHASE ORDER</h3>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{date('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <!-- Supplier Table -->
    <table class="supplier-table">
        <tbody>
            <tr>
                <td style="width: 40px; padding-left: 10px;">Order No : {{$data[0]->order['order_number']}}</td>
                <td style="width: 110px; padding-left: 10px;">Supplier : {{$data[0]->order->supplier->name}}</td>
            </tr>
            <tr>
                <td style="width: 40px; padding-left: 10px;">Order Date : {{date('Y-m-d', strtotime($data[0]->order['ordered_at']))}}</td>
                <td style="width: 80px; padding-left: 10px;">TIN : {{$pharmacy['tin_number'] ?? 'N/A'}}</td>
            </tr>
        </tbody>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr class="table-header">
                <th style="width: 20px; text-align: center;">#</th>
                <th style="width: 287.5px; text-align: left; padding-left: 7px;">Product Name</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="width: 83px; text-align: right;">Price</th>
                <th style="width: 83px; text-align: right;">VAT</th>
                <th style="width: 83px; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr>
                    <td style="width: 20px; text-align: center;">{{$loop->iteration}}.</td>
                    <td style="width: 287.5px; text-align: left; padding-left: 7px;">{{$item->product['name']}}</td>
                    <td style="width: 50px; text-align: center;">{{number_format($item->ordered_qty, 0)}}</td>
                    <td style="width: 82.5px; text-align: right;">{{number_format($item->unit_price, 2)}}</td>
                    <td style="width: 82.5px; text-align: right;">{{number_format($item->vat, 2)}}</td>
                    <td style="width: 82.5px; text-align: right;">{{number_format($item->amount, 2)}}</td>
                </tr>
            @endforeach

            @if(count($data) < 5)
                <!-- Empty rows for spacing -->
                @for($i = 0; $i < 7 - count($data); $i++)
                    <tr>
                        <td class="index-col"></td>
                        <td class="description-col">&nbsp;</td>
                        <td class="qty-col">&nbsp;</td>
                        <td class="unit-col">&nbsp;</td>
                        <td class="vat-col">&nbsp;</td>
                        <td class="amount-col">&nbsp;</td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <div style="display: inline-flex;">
        <div>
            <div class="footer-section">
                <div class="issued-by">Ordered By:</div>
                <span style="font-size: 10px; border-bottom: 1px solid #ccc;">Printed on: {{date('Y-m-d H:i:s')}}</span>
            </div>

            @if($generalSettings && $generalSettings->purchase_order_terms)
                <div style="padding-top: 10px;">
                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 5px;">Terms & Conditions:</div>
                    <div style="font-size: 11px; line-height: 1.4; text-align: justify;">
                        {!! nl2br(e($generalSettings->purchase_order_terms)) !!}
                    </div>
                </div>
            @endif
        </div>
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-row">
                <div>Sub Total:</div>
                <div style="float: right;">{{number_format($data->max('sub_totals'), 2)}}</div>
            </div>
            <div class="summary-row">
                <span>VAT:</span>
                <span style="float: right;">{{number_format($data->max('vats'), 2)}}</span>
            </div>
            <div class="summary-row total">
                <span>Total Amount:</span>
                <span style="float: right;">{{number_format($data->max('total'), 2)}}</span>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="slogan-section">
        {{$pharmacy['slogan'] ?? 'Thank you for your business'}}
    </div>
</body>
</html>