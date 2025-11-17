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
    <title>Receipt</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 10px;
            margin: 0;
            padding: 10px 30px 10px 10px;
            /* font-weight: bold; */
        }

        * {
            font-family: Arial, Helvetica, sans-serif
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th,
        td {
            padding: 2px;
            word-wrap: break-word;
        }

        #table-detail thead th {
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
            border-bottom: 1px dashed #000;
            margin: 4px 0;
        }

        h3,
        h4,
        h5,
        h6 {
            margin: 2px 0;
            font-weight: normal;
            text-align: center;
        }
    </style>
</head>

<body>
    <div style="width: 100%;">
        <h3><b>CASH RECEIPT</b></h3>
        <h4>{{$pharmacy['name']}}</h4>
        <h5>{{$pharmacy['address']}}</h5>
        <h5>{{$pharmacy['phone']}}</h5>
        <h5>TIN: {{$pharmacy['tin_number'] ?? 'N/A'}}</h5>
        {{-- @dd($data) --}}
        @foreach($data as $datas => $dat)
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> {{$datas}}<br>
                        <span>Sales Date:</span> {{date('Y-m-d', strtotime($dat[0]['created_at']))}}<br>
                        <span>Customer:</span> {{$dat[0]['customer'] ?? 'CASH'}}<br>
                        <span>TIN:</span> {{$dat[0]['customer_tin'] ?? 'N/A'}}<br>
                        <span>Printed On:</span> {{date('Y-m-d H:i:s')}}
                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr>
                        <th align="left" style="width: 45%;">Description</th>
                        <th align="center" style="width: 15%;">Qty</th>
                        <th align="right" style="width: 40%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dat as $item)
                        <tr>
                            <td>{{$item['name']}} {{$item['brand'] ?? ''}} {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}</td>
                            <td align="center">{{customRound($item['quantity'])}}</td>
                            <td align="right">{{customRound($item['price'] * $item['quantity'])}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <table id="footer-detail">
                <tbody>
                        <tr>
                            <td align="left">Sub Total</td>
                            <td align="right">{{customRound($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total'])}}</td>
                        </tr>
                        @if($dat[0]['discount_total'] > 0)
                            <tr>
                                <td align="left">Discount</td>
                                <td align="right">{{customRound($dat[0]['discount_total'])}}</td>
                            </tr>
                        @endif
                        <tr>
                            <td align="left">VAT</td>
                            <td align="right">{{customRound($dat[0]['total_vat'])}}</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Total</b></td>
                            <td align="right"><b>{{customRound($dat[0]['grand_total'])}}</b></td>
                        </tr>
                </tbody>
            </table>
            <hr>
            <h5><b>Issued By: {{$dat[0]['sold_by']}}</b></h5>
            <h5 style="font-style: italic; font-weight: bold;">{{$pharmacy['slogan']}}</h5>
        @endforeach
    </div>
</body>

</html>