@php
function customRound($num) {
    $whole = floor($num);
    $decimal = $num - $whole;

    if ($decimal > 0.5) {
        return number_format($whole + 1, 2);
    } else {
        return number_format($whole, 2);
    }
}
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Proforma Invoice</title>
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

        .sold-by {
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
    <div style="font-weight: bold; margin-top: 5px; text-align: center;">
        PROFORMA INVOICE
    </div>

    @foreach($data as $datas => $dat)
        <table class="customer-table">
            <tbody>
                <tr style="width: 100%; position: relative;">
                    <td style="width: 21%; position: absolute; padding-left: 10px;">Receipt No : {{$datas ?? 'N/A'}}</td>
                    <td style="width: 43.5%; padding-left: 10px;">Customer : {{$dat[0]['customer'] ?? 'CASH'}}</td>
                    <td style="width: 30%; padding-left: 10px;">Phone : {{$dat[0]['customer_phone'] ?? 'N/A'}}</td>
                </tr>
                <tr style="width: 100%; position: relative;">
                    <td style="width: 21%; padding-left: 10px;">Sales Date :
                        {{date('Y-m-d', strtotime($dat[0]['created_at']))}}
                    </td>
                    <td style="width: 43.5%; padding-left: 10px;">Address : {{$dat[0]['customer_address'] ?? 'N/A'}}</td>
                    <td style="width: 30%; padding-left: 10px;">TIN :
                        {{ !empty($dat[0]['customer_tin']) ? $dat[0]['customer_tin'] : 'N/A' }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach
    @php
        $subTotal = 0;
        $vat = 0;
        $discount = 0;
        $grandTotal = 0;
    @endphp

    <!-- Customer Information -->
    @foreach($data as $datas => $dat)
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr class="table-header" style="width: 100%; position: relative;">
                    <th style="width: 2%; position: absolute; text-align: center;">#</th>
                    <th style="width: 52%; position: absolute; text-align: left; padding-left: 7px;">Description</th>
                    <th style="width: 10%; position: absolute; text-align: center;">Qty</th>
                    <th style="width: 18%; position: absolute; text-align: right;">Price</th>
                    <th style="width: 18%; position: absolute; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dat as $item)
                    {{-- @dd($item) --}}
                    <tr>
                        <td style="width: 20px; text-align: center;">{{$loop->iteration}}.</td>
                        <td style="width: 287.5px; text-align: left; padding-left: 7px;">{{$item['name']}}
                            {{$item['brand'] ?? ''}}
                            {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}
                        </td>
                        <td style="width: 48.5px; text-align: center;">{{customRound($item['quantity'])}}</td>
                        <td style="width: 82.5px; text-align: right;">{{customRound($item['price'])}}</td>
                        <td style="width: 82.5px; text-align: right;">{{customRound($item['price'] * $item['quantity'])}}
                        </td>
                    </tr>
                    @php
                        $subTotal += $item['sub_total'];
                        $vat += $item['vat'];
                        $discount += $item['discount'];
                        $grandTotal += ($item['sub_total'] - $item['discount']) + $item['vat'];
                    @endphp
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
        <div style="display: flex; justify-content: space-between; width: 100%;">
            <div style="width: 50%;">
                <div class="footer-section">
                    @foreach($data as $datas => $dat)
                        <div class="sold-by">Issued By: {{$dat[0]['sold_by']}}</div>
                        @break
                    @endforeach
                    <span style="font-size: 10px; border-bottom: 1px solid #ccc;">Printed on: {{date('Y-m-d H:i:s')}}</span>
                </div>

                @if($generalSettings && $generalSettings->proforma_invoice_terms)
                    <div style="padding-top: 10px;">
                        <div style="font-weight: bold; font-size: 13px; margin-bottom: 5px;">Terms & Conditions:</div>
                        <div style="font-size: 12px; line-height: 1.4; text-align: justify;">
                            {!! nl2br(e($generalSettings->proforma_invoice_terms)) !!}
                        </div>
                    </div>
                @endif
            </div>
            <div class="summary-section" style="width: 40%; margin-top: -50px; padding-top: 0; float: right;">
                <div class="summary-row">
                    <div>Sub Total:</div>
                    <div style="float: right;">
                        {{customRound(($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total']))}}
                    </div>
                </div>
                <div class="summary-row">
                    <span>VAT:</span>
                    <span style="float: right;">{{customRound($dat[0]['total_vat'])}}</span>
                </div>
                @if($dat[0]['discount_total'] > 0)
                    <div class="summary-row">
                        <div>Discount:</div>
                        <div style="float: right;">{{customRound($dat[0]['discount_total'])}}</div>
                    </div>
                @endif
                <div class="summary-row total">
                    <span>Total:</span>
                    <span style="float: right;">{{customRound($dat[0]['grand_total'])}}</span>
                </div>
                @if($page == -1)
                    <div class="summary-row" style="margin-top: 10px;">
                        <span>Paid:</span>
                        <span style="float: right;">{{customRound($dat[0]['paid'])}}</span>
                    </div>
                    <div class="summary-row">
                        <span>Balance:</span>
                        <span style="float: right;">{{customRound($dat[0]['grand_total'] - $dat[0]['paid'])}}</span>
                    </div>
                @endif
            </div>
        </div>
        @break
    @endforeach
    <div class="slogan-section">
        {{$pharmacy['slogan'] ?? 'Thank you for your business'}}
    </div>
</body>

</html>