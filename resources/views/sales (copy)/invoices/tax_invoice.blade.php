<!DOCTYPE html>
<html>
<head>
    <title>Tax Invoice</title>
    <style>
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
            font-size: x-small;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 13px;
            width: 50%;
        }

        .col-100 {
            display: inline-block;
            font-size: 13px;
            width: 90%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

        #table-detail {
            border-spacing: 6%;
            width: 96%;
            margin-top: 2%;
            border: none;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 160px;
            max-height: 160px;
        }

        .invoice-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #007bff;
            border-radius: 5px;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .tax-summary {
            background-color: #e9ecef;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
    </style>
</head>
<body>
<div class="row">
    <div id="container">
        @if($pharmacy['logo'])
            <div class="logo-container">
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            </div>
        @endif
    </div>
</div>

<div class="row" style="padding-top: -2%">
    <h2 align="center" style="color: #007bff; margin-bottom: 0;">TAX INVOICE</h2>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['name']}}</h3>
    <h5 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h5>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h6>
    <h5 align="center" style="margin-top: -2%">TIN: {{$pharmacy['tin_number']}}</h5>
    <h5 align="center" style="margin-top: -2%">VRN: {{$pharmacy['vrn_number']}}</h5>
</div>

<div class="invoice-header">
    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
        <div style="flex: 1;">
            <strong>Invoice #:</strong> {{$tax_invoice_number}}<br>
            <strong>Delivery Note #:</strong> {{$sale->delivery_note_number ?? 'N/A'}}<br>
            <strong>Sale Receipt #:</strong> {{$sale->receipt_number}}<br>
            <strong>Invoice Date:</strong> {{date('j M, Y', strtotime($sale->date))}}
        </div>
        <div style="text-align: right;">
            <strong>Payment Type:</strong> {{$sale->payment_type_id == 1 ? 'Cash Sale' : 'Credit Sale'}}<br>
            <strong>Sold By:</strong> {{$sale->user->name ?? 'N/A'}}<br>
            <strong>Created:</strong> {{date('j M, Y H:i', strtotime($sale->date))}}
        </div>
    </div>
</div>

<div style="margin: 20px 0; font-size: 14px;">
    <div style="display: flex; justify-content: space-between;">
        <div style="width: 48%;">
            <h4 style="margin-bottom: 10px; color: #007bff;">BILL TO:</h4>
            <strong>{{$sale->customer->name ?? 'Walk-in Customer'}}</strong><br>
            @if($sale->customer && $sale->customer->address)
                {{$sale->customer->address}}<br>
            @endif
            @if($sale->customer && $sale->customer->phone)
                Phone: {{$sale->customer->phone}}<br>
            @endif
            @if($sale->customer && $sale->customer->tin)
                <strong>Customer TIN:</strong> {{$sale->customer->tin}}
            @else
                <strong>Customer TIN:</strong> N/A
            @endif
        </div>
        <div style="width: 48%; text-align: right;">
            <h4 style="margin-bottom: 10px; color: #007bff;">INVOICE DETAILS:</h4>
            <strong>Due Date:</strong> {{$sale->payment_type_id == 1 ? 'Immediate' : date('j M, Y', strtotime($sale->date . ' +30 days'))}}<br>
            <strong>Terms:</strong> {{$sale->payment_type_id == 1 ? 'Cash on Delivery' : 'Net 30 Days'}}<br>
            <strong>Status:</strong> <span style="color: #28a745;">Paid</span>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <table class="table table-sm" id="table-detail" align="center">
        <tr style="background-color: #007bff; color: white;">
            <th align="left">S/N</th>
            <th align="left">Description</th>
            <th align="center">Quantity</th>
            <th align="right">Unit Price</th>
            <th align="right">VAT</th>
            <th align="right">Discount</th>
            <th align="right">Amount</th>
        </tr>

        @php
            $sn = 1;
            $total_amount = 0;
            $total_vat = 0;
            $total_discount = 0;
            $subtotal = 0;
        @endphp

        @foreach($sale_details as $item)
            @php
                $unit_price = $item->price / $item->quantity;
                $total_amount += $item->amount;
                $total_vat += $item->vat;
                $total_discount += $item->discount;
                $subtotal += ($item->amount - $item->vat);
            @endphp
            <tr>
                <td align="left">{{$sn++}}</td>
                <td align="left">{{$item->product_name}}</td>
                <td align="center">{{number_format($item->quantity, 0)}}</td>
                <td align="right">{{number_format($unit_price, 2)}}</td>
                <td align="right">{{number_format($item->vat, 2)}}</td>
                <td align="right">{{number_format($item->discount, 2)}}</td>
                <td align="right">{{number_format($item->amount, 2)}}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="tax-summary" style="margin-top: 30px;">
    <table style="width: 100%; border: none; font-size: 14px;">
        <tr style="border: none;">
            <td style="border: none; width: 60%;"></td>
            <td style="border: none; width: 40%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right; padding: 5px;"><strong>Subtotal (Before VAT):</strong></td>
                        <td style="text-align: right; padding: 5px;">{{number_format($subtotal, 2)}}</td>
                    </tr>
                    @if($total_discount > 0)
                    <tr>
                        <td style="text-align: right; padding: 5px;"><strong>Total Discount:</strong></td>
                        <td style="text-align: right; padding: 5px; color: #dc3545;">-{{number_format($total_discount, 2)}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="text-align: right; padding: 5px;"><strong>Total VAT:</strong></td>
                        <td style="text-align: right; padding: 5px;">{{number_format($total_vat, 2)}}</td>
                    </tr>
                    <tr style="border-top: 2px solid #007bff; background-color: #f8f9fa;">
                        <td style="text-align: right; padding: 10px; font-size: 16px;"><strong>TOTAL AMOUNT:</strong></td>
                        <td style="text-align: right; padding: 10px; font-size: 16px; color: #007bff;"><strong>{{number_format($total_amount, 2)}}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div style="margin-top: 40px; font-size: 12px; color: #666;">
    <h4 style="color: #007bff;">TERMS & CONDITIONS:</h4>
    <p>1. All goods are supplied on {{$pharmacy['name']}} sale basis.</p>
    <p>2. Account holder invoices are due strictly 30 days from the date of invoice.</p>
    <p>3. All overdue accounts the company reserves right to charge interest at 3% rate of total invoice.</p>
    <p>4. This is a system generated invoice and is valid without signature.</p>
    @if($pharmacy['slogan'])
        <p style="text-align: center; font-style: italic; margin-top: 20px;">{{$pharmacy['slogan']}}</p>
    @endif
</div>

<div style="margin-top: 30px; text-align: center; font-size: 11px; color: #999;">
    <p>Generated on {{date('j M, Y H:i:s')}} | This is a computer generated document</p>
</div>

</body>
</html> 