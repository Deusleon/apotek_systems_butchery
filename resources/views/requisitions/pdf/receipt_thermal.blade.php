<!DOCTYPE html>
<html>
<head>
    <title>Requisition</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #000;
        }

        h2, h3, h4, h5 {
            margin: 2px 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 20px;
            font-weight: bold;
        }

        .header h4 {
            font-weight: normal;
            font-size: 13px;
        }

        .req-info {
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .req-info td {
            padding: 4px 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        #items th, #items td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 12px;
        }

        #items th {
            background-color: #f5f5f5;
            text-align: left;
        }

        #items tr:nth-child(even) {
            background-color: #fafafa;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
        }

        .slogan {
            font-style: italic;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>REQUISITION</h2>
        <h3>{{ $pharmacy['name'] }}</h3>
        <h4>{{ $pharmacy['address'] }}</h4>
        <h4>{{ $pharmacy['phone'] }}</h4>
        @if(!empty($pharmacy['tin_number']))
            <h4>TIN: {{ $pharmacy['tin_number'] }}</h4>
        @endif
    </div>

    <table class="req-info">
        <tr>
            <td><b>Requisition #:</b> {{ $requisition->req_no ?? '' }}</td>
            <td><b>Date:</b> {{ date('j M, Y', strtotime($requisition->created_at)) }}</td>
        </tr>
        <tr>
            <td><b>Created By:</b> {{ $requisition->creator->name }}</td>
        </tr>
    </table>

    <table id="items">
        <thead>
            <tr>
                <th style="width: 5%">S/N</th>
                <th style="width: 70%">Product</th>
                <th style="width: 25%">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisitionDet as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->products_->name ?? '' }}
                        @if(!empty($item->products_->brand)) {{ $item->products_->brand }} @endif
                        @if(!empty($item->products_->pack_size) && !empty($item->products_->sales_uom))
                            {{ $item->products_->pack_size }}{{ $item->products_->sales_uom }}
                        @endif
                    </td>
                    <td>{{ number_format($item->quantity, 0) ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Created By: <b>{{ $requisition->creator->name }}</b></p>
        @if(!empty($pharmacy['slogan']))
            <p class="slogan">{{ $pharmacy['slogan'] }}</p>
        @endif
    </div>

</body>
</html>
