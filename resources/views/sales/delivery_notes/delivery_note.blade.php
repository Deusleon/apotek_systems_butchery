<!DOCTYPE html>
<html>
<head>
    <title>Delivery Note</title>
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

        .delivery-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #28a745;
            border-radius: 5px;
        }

        .delivery-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .signature-section {
            background-color: #e9ecef;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }

        .signature-box {
            border: 1px solid #ccc;
            height: 60px;
            margin: 10px 0;
            padding: 5px;
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
    <h2 align="center" style="color: #28a745; margin-bottom: 0;">DELIVERY NOTE</h2>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['name']}}</h3>
    <h5 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h5>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h6>
    <h5 align="center" style="margin-top: -2%">TIN: {{$pharmacy['tin_number']}}</h5>
    <h5 align="center" style="margin-top: -2%">VRN: {{$pharmacy['vrn_number']}}</h5>
</div>

<div class="delivery-header">
    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
        <div style="flex: 1;">
            <strong>Delivery Note #:</strong> {{$delivery_note_number}}<br>
            <strong>Tax Invoice #:</strong> {{$sale->tax_invoice_number ?? 'N/A'}}<br>
            <strong>Sale Receipt #:</strong> {{$sale->receipt_number}}<br>
            <strong>Delivery Date:</strong> {{date('j M, Y', strtotime($sale->date))}}
        </div>
        <div style="text-align: right;">
            <strong>Payment Type:</strong> {{$sale->payment_type_id == 1 ? 'Cash Sale' : 'Credit Sale'}}<br>
            <strong>Prepared By:</strong> {{$sale->user->name ?? 'N/A'}}<br>
            <strong>Time:</strong> {{date('H:i', strtotime($sale->date))}}
        </div>
    </div>
</div>

<div style="margin: 20px 0; font-size: 14px;">
    <div style="display: flex; justify-content: space-between;">
        <div style="width: 48%;">
            <h4 style="margin-bottom: 10px; color: #28a745;">DELIVER TO:</h4>
            <strong>{{$sale->customer->name ?? 'Walk-in Customer'}}</strong><br>
            @if($sale->customer && $sale->customer->address)
                {{$sale->customer->address}}<br>
            @endif
            @if($sale->customer && $sale->customer->phone)
                Phone: {{$sale->customer->phone}}<br>
            @endif
            @if($sale->customer && $sale->customer->email)
                Email: {{$sale->customer->email}}<br>
            @endif
        </div>
        <div style="width: 48%; text-align: right;">
            <h4 style="margin-bottom: 10px; color: #28a745;">DELIVERY DETAILS:</h4>
            <strong>Delivery Method:</strong> {{$sale->payment_type_id == 1 ? 'Counter Collection' : 'Standard Delivery'}}<br>
            <strong>Expected Delivery:</strong> {{date('j M, Y', strtotime($sale->date . ' +1 day'))}}<br>
            <strong>Priority:</strong> Normal<br>
            <strong>Status:</strong> <span style="color: #28a745;">Ready for Delivery</span>
        </div>
    </div>
</div>

<div class="row" style="margin-top: 20px;">
    <table class="table table-sm" id="table-detail" align="center">
        <tr style="background-color: #28a745; color: white;">
            <th align="left">S/N</th>
            <th align="left">Product Description</th>
            <th align="center">Quantity Ordered</th>
            <th align="center">Quantity Delivered</th>
            <th align="center">Unit</th>
            <th align="left">Remarks</th>
        </tr>

        @php
            $sn = 1;
            $total_quantity = 0;
        @endphp

        @foreach($sale_details as $item)
            @php
                $total_quantity += $item->quantity;
            @endphp
            <tr>
                <td align="left">{{$sn++}}</td>
                <td align="left">{{$item->product_name}}</td>
                <td align="center">{{number_format($item->quantity, 0)}}</td>
                <td align="center">{{number_format($item->quantity, 0)}}</td>
                <td align="center">PCS</td>
                <td align="left">Good Condition</td>
            </tr>
        @endforeach
        
        <tr style="background-color: #f8f9fa; font-weight: bold;">
            <td colspan="2" align="right"><strong>TOTAL ITEMS:</strong></td>
            <td align="center"><strong>{{number_format($total_quantity, 0)}}</strong></td>
            <td align="center"><strong>{{number_format($total_quantity, 0)}}</strong></td>
            <td colspan="2"></td>
        </tr>
    </table>
</div>

<div style="margin-top: 30px; font-size: 12px; color: #666;">
    <h4 style="color: #28a745;">DELIVERY INSTRUCTIONS:</h4>
    <p>1. Any claim relating to shortages or damage to the goods caused by transit must be notified to the company within 3 working days of delivery.</p>
    <p>2. The company will endeavor to replace damaged goods or if this is not possible a credit note will be issued.</p>
    <p>3. The quantity, quality and description of any specifications for the Goods shall be those set out in the {{$pharmacy['name']}} quotation.</p>
    <p>4. Please check all items before signing for delivery.</p>
</div>

@if($generalSettings && $generalSettings->delivery_note_terms)
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px;">
        <h4 style="color: #28a745; margin-bottom: 10px;">TERMS & CONDITIONS:</h4>
        <div style="font-size: 11px; line-height: 1.5; text-align: justify; color: #333;">
            {!! nl2br(e($generalSettings->delivery_note_terms)) !!}
        </div>
    </div>
@endif

<div class="signature-section">
    <div style="display: flex; justify-content: space-between;">
        <div style="width: 48%;">
            <h4 style="margin-bottom: 10px; color: #28a745;">DELIVERED BY:</h4>
            <div class="signature-box">
                <div style="margin-top: 40px;">
                    <strong>Name:</strong> _________________________<br>
                    <strong>Signature:</strong> _____________________<br>
                    <strong>Date:</strong> {{date('j M, Y')}} <strong>Time:</strong> ___________
                </div>
            </div>
        </div>
        <div style="width: 48%;">
            <h4 style="margin-bottom: 10px; color: #28a745;">RECEIVED BY:</h4>
            <div class="signature-box">
                <div style="margin-top: 40px;">
                    <strong>Name:</strong> _________________________<br>
                    <strong>Signature:</strong> _____________________<br>
                    <strong>Date:</strong> _____________ <strong>Time:</strong> ___________
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top: 20px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 3px;">
    <h4 style="color: #856404; margin-bottom: 10px;">IMPORTANT NOTES:</h4>
    <ul style="color: #856404; margin: 0; padding-left: 20px;">
        <li>This delivery note must be signed by the recipient</li>
        <li>Keep this document for your records</li>
        <li>Report any discrepancies immediately</li>
        <li>Contact {{$pharmacy['phone']}} for any delivery issues</li>
    </ul>
</div>

<div style="margin-top: 30px; text-align: center; font-size: 11px; color: #999;">
    <p>Generated on {{date('j M, Y H:i:s')}} | This is a computer generated document</p>
    @if($pharmacy['slogan'])
        <p style="font-style: italic;">{{$pharmacy['slogan']}}</p>
    @endif
</div>

</body>
</html> 