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
            padding: 10px;
        }

        * {
            font-family: 'Courier New', monospace;
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
        <h5>TIN: {{$pharmacy['tin_number']}}</h5>

        @foreach($data as $datas => $dat)
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> {{$datas}}<br>
                        <span>Customer:</span> {{$dat[0]['customer'] ?? 'CASH'}}<br>
                        <span>TIN:</span> {{$dat[0]['customer_tin']}}<br>
                        <span>Date:</span> {{date('Y-m-d', strtotime($dat[0]['created_at']))}}
                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr>
                        <th align="left">Description</th>
                        <th align="center">Qty</th>
                        <th align="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dat as $item)
                        <tr>
                            <td>{{$item['name']}} {{$item['pack_size']}}{{$item['sales_uom']}}</td>
                            <td align="center">{{number_format($item['quantity'], 0)}}</td>
                            <td align="right">{{number_format($item['price'] * $item['quantity'], 2)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr>
            <table id="footer-detail">
                <tbody>
                        <tr>
                            <td align="left">Sub Total</td>
                            <td align="right">{{number_format($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total'], 2)}}</td>
                        </tr>
                        @if($dat[0]['discount_total'] > 0)
                            <tr>
                                <td align="left">Discount</td>
                                <td align="right">{{number_format($dat[0]['discount_total'], 2)}}</td>
                            </tr>
                        @endif
                        <tr>
                            <td align="left">VAT</td>
                            <td align="right">{{number_format($dat[0]['total_vat'], 2)}}</td>
                        </tr>
                        <tr>
                            <td align="left"><b>Total</b></td>
                            <td align="right"><b>{{number_format($dat[0]['grand_total'], 2)}}</b></td>
                        </tr>
                </tbody>
            </table>
            <hr>
            <h5>Issued By {{$dat[0]['sold_by']}}</h5>
            <h5 style="font-style: italic">{{$pharmacy['slogan']}}</h5>
        @endforeach
    </div>
</body>

</html>