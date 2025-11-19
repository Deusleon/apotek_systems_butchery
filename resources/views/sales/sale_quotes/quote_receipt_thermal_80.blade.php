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
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 18px;
            margin: 0;
            padding: 12px;
            padding-right: 30px;
        }

        * {
            font-family: Arial, Helvetica, sans-serif
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding: 2px;
            word-wrap: break-word;
        }

        #table-detail thead th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        
        #table-detail tbody tr td {
            border-bottom: 1px dotted #000;
        }

        #table-detail tbody tr:last-child td {
            border-bottom: none;
        }

        hr {
            border: none;
            border-bottom: 1px solid #000;
            margin: 3px 0;
        }

        h3,
        h4,
        h5,
        h6 {
            margin: 2px 0;
            font-weight: normal;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        #footer-detail td {
            padding: 2px 0;
        }
    </style>
</head>

<body>
    <div style="width: 100%;">
        <h3><b>PROFORMA INVOICE</b></h3>
        <h4>{{$pharmacy['name']}}</h4>
        <h5>{{$pharmacy['address']}}</h5>
        <h5>{{$pharmacy['phone']}}</h5>
        <h5>TIN: {{$pharmacy['tin_number'] ?? 'N/A'}}</h5>
        <h5>VRN: {{$pharmacy['vrn_number'] ?? 'N/A'}}</h5>
        @php
            $subTotal = 0;
            $vat = 0;
            $discount = 0;
            $grandTotal = 0;
        @endphp
        @foreach($data as $datas => $dat)
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> {{$datas}}<br>
                        <span>Date:</span> {{date('Y-m-d', strtotime($dat[0]['created_at']))}}<br>
                        <span>Customer:</span> {{$dat[0]['customer'] ?? 'CASH'}}<br>
                        <span>Customer TIN:</span> {{ !empty($dat[0]['customer_tin']) ? $dat[0]['customer_tin'] : 'N/A' }}<br>
                        <span>Printed On:</span> {{date('Y-m-d H:i:s')}}
                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr style="border-bottom: 1px dotted #000">
                        <th align="left" style="width: 35%;">Description</th>
                        <th class="text-center" style="width: 15%;">Qty</th>
                        <th class="text-right" style="width: 20%;">Price</th>
                        {{-- <th class="text-right">VAT</th> --}}
                        <th class="text-right" style="width: 30%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dat as $item)
                        <tr>
                            <td>{{$item['name']}} {{$item['brand'] ?? ''}}
                                {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}
                            </td>
                            <td class="text-center">{{number_format($item['quantity'], 0)}}</td>
                            <td class="text-right">{{customRound($item['price'])}}</td>
                            {{-- <td class="text-right">{{customRound($item['vat'])}}</td> --}}
                            <td class="text-right">{{customRound($item['quantity'] * $item['price'])}}</td>
                        </tr>
                        @php
                            $subTotal += $item['sub_total'];
                            $vat += $item['vat'];
                            $discount += $item['discount'];
                            $grandTotal += ($item['sub_total'] - $item['discount']) + $item['vat'];
                        @endphp
                    @endforeach
                </tbody>
            </table>

            <hr>
            <table id="footer-detail">
                <tbody>
                    <tr>
                        <td>Sub Total</td>
                        <td class="text-right">
                            {{customRound($subTotal)}}
                        </td>
                    </tr>
                    <tr>
                        <td>VAT</td>
                        <td class="text-right">{{customRound($vat)}}</td>
                    </tr>
                    @if($dat[0]['discount_total'] > 0)
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">{{customRound($discount)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td><b>Total</b></td>
                        <td class="text-right"><b>{{customRound($grandTotal)}}</b></td>
                    </tr>
                </tbody>
            </table>

            <hr>
            <h5>Issued By: {{$dat[0]['sold_by']}}</h5>
            <h5 style="font-style: italic;">{{$pharmacy['slogan'] ?? 'Thank you for your business'}}</h5>
        @endforeach
    </div>
</body>

</html>