<!DOCTYPE html>
<html>
<head>
    <title>Stock Discrepancy Report</title>

    <style>
        body {
            font-family: sans-serif;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
            font-size: 10px;
        }

        table {
            width: 100%;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-section img {
            max-height: 80px;
        }

        .header-section h3, .header-section h6 {
            margin: 0;
        }

        .report-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .topcorner {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: 20px;
            margin-right: 20px;
        }

        .topcorner > p {
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="header-section">
    @if($pharmacy['logo'])
        <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}" alt="Pharmacy Logo"/>
    @endif
    <h3>{{$pharmacy['name']}}</h3>
    <h6>{{$pharmacy['address']}}</h6>
    @if($pharmacy['phone'])<h6>Phone: {{$pharmacy['phone']}}</h6>@endif
    @if($pharmacy['email'])<h6>Email: {{$pharmacy['email']}}</h6>@endif
    @if($pharmacy['website'])<h6>Website: {{$pharmacy['website']}}</h6>@endif
    @if($pharmacy['tin_number'])<h6>TIN: {{$pharmacy['tin_number']}}</h6>@endif
</div>

<h3 class="report-title">Stock Discrepancy Report</h3>

<div class="topcorner">
    <p>Date: {{ date('Y-m-d H:i:s') }}</p>
</div>

<table>
    <thead>
    <tr style="background: #1f273b; color: white;">
        <th>Date</th>
        <th>Product Name</th>
        <th>Brand</th>
        <th>Pack Size</th>
        <th>Previous Qty</th>
        <th>New Qty</th>
        <th>Adjusted Qty</th>
        <th>Type</th>
        <th>Reason</th>
        <th>Notes</th>
        <th>Adjusted By</th>
    </tr>
    </thead>
    <tbody>
    @foreach($discrepancies as $discrepancy)
        <tr>
            <td>{{ date('Y-m-d', strtotime($discrepancy->created_at)) }}</td>
            <td>{{ $discrepancy->currentStock->product->name ?? 'N/A' }}</td>
            <td>{{ $discrepancy->currentStock->product->brand ?? 'N/A' }}</td>
            <td>{{ $discrepancy->currentStock->product->pack_size ?? 'N/A' }}</td>
            <td>{{ number_format($discrepancy->previous_quantity, 0) }}</td>
            <td>{{ number_format($discrepancy->new_quantity, 0) }}</td>
            <td>{{ number_format($discrepancy->adjustment_quantity, 0) }}</td>
            <td>{{ ucfirst($discrepancy->adjustment_type) }}</td>
            <td>{{ $discrepancy->reason }}</td>
            <td>{{ $discrepancy->notes ?? 'N/A' }}</td>
            <td>{{ $discrepancy->user->name ?? 'N/A' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 280;
        $y = 820;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
    }
</script>

</body>
</html> 