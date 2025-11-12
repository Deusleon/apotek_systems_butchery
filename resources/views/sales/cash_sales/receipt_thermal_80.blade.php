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
            /* font-weight: bold; */
        }

        * {
            font-family: Arial, Helvetica, sans-serif
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
        
        #table-detail tbody tr td {
            border-bottom: 1px dotted #000;
        }

        #table-detail tbody tr:last-child td {
            border-bottom: none;
        }        

        hr {
            border: none;
            border-bottom: 1px dashed #000;
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

        /* Align numeric columns */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Reduce spacing for thermal */
        #footer-detail td {
            padding: 2px 0;
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
                        <th align="left" style="width: 50%;">Description</th>
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
            <hr>
            <h5>Issued By: {{$dat[0]['sold_by']}}</h5>
            <h5 style="font-style: italic;">{{$pharmacy['slogan'] ?? 'Thank you for your business'}}</h5>
        @endforeach
    </div>
</body>

</html>
