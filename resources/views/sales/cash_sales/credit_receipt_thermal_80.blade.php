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
            font-size: 12px;
            margin: 0;
            padding: 12px;
            font-weight: bold;
        }

        * {
            font-family: 'Courier New', monospace;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
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
            border-top: 1px dashed #000;
            margin: 3px 0;
        }

        h3,
        h4,
        h5,
        h6 {
            margin: 2px 0;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer table spacing */
        #footer-detail td {
            padding: 2px 0;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <div style="width: 100%;">
        <h3><b>CREDIT RECEIPT</b></h3>
        <h4>{{$pharmacy['name']}}</h4>
        <h5>{{$pharmacy['address']}}</h5>
        <h5>{{$pharmacy['phone']}}</h5>
        <h5>TIN: {{$pharmacy['tin_number'] ?? 'N/A' }}</h5>

        @foreach($data as $datas => $dat)
            <table>
                <tr>
                    <td>
                        <span>Receipt #:</span> {{$datas}}<br>
                        <span>Customer:</span> {{$dat[0]['customer'] ?? 'CASH'}}<br>
                        <span>TIN:</span> {{$dat[0]['customer_tin'] ?? 'N/A'}}<br>
                        <span>Date:</span> {{date('Y-m-d H:i:s')}}
                    </td>
                </tr>
            </table>

            <table id="table-detail">
                <thead>
                    <tr>
                        <th align="left">Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dat as $item)
                        <tr>
                            <td>{{$item['name']}} {{$item['brand'] ?? ''}} {{$item['pack_size'] ?? ''}}{{$item['sales_uom'] ?? ''}}</td>
                            <td class="text-center">{{number_format($item['quantity'], 0)}}</td>
                            <td class="text-right">{{number_format($item['price'] * $item['quantity'], 2)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>
            <table id="footer-detail">
                <tbody>
                    <tr>
                        <td>Sub Total</td>
                        <td class="text-right">{{number_format($dat[0]['grand_total'] - $dat[0]['total_vat'] + $dat[0]['discount_total'], 2)}}</td>
                    </tr>
                    @if($dat[0]['discount_total'] > 0)
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">{{number_format($dat[0]['discount_total'], 2)}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>VAT</td>
                        <td class="text-right">{{number_format($dat[0]['total_vat'], 2)}}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td class="text-right"><b>{{number_format($dat[0]['grand_total'], 2)}}</b></td>
                    </tr>
                </tbody>
            </table>

            @if($page === "-1")
                <hr>
                <table id="footer-detail">
                    <tbody>
                        <tr>
                            <td>Paid</td>
                            <td class="text-right">{{number_format($dat[0]['paid'], 2)}}</td>
                        </tr>
                        <tr>
                            <td>Balance</td>
                            <td class="text-right">{{number_format($dat[0]['grand_total'] - $dat[0]['paid'], 2)}}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <div class="summary-row">
                    <span>Remark:</span>
                    <span>{{$dat[0]['remark'] ?? ''}}</span>
                </div>
            @endif

            <h5>Issued By: {{$dat[0]['sold_by']}}</h5>
            <h5 style="font-style: italic;">{{$pharmacy['slogan'] ?? 'Thank you for your business'}}</h5>
        @endforeach
    </div>
</body>

</html>
