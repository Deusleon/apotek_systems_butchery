<!DOCTYPE html>
<html>

<head>
    <title>Stock Transfer Report</title>

    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th,
        td {
            border-collapse: collapse;
            padding: 8px;
        }

        th {
            text-align: left;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group;
            background: #1f273b;
            color: white;
            font-size: 12px;
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
            margin-top: -13%;
            border-collapse: collapse;
        }

        #table-detail tr {
            line-height: 10px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 100px;
            max-height: 100px;
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
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Stock Transfer Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d', strtotime($data[0]['from']))}}</b> To:
                <b>{{date('Y-m-d', strtotime($data[0]['to']))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <div class="row" style="margin-top: 6%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <!-- loop the product names here -->
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="center">#</th>
                            <th align="left">Date</th>
                            <th align="left">Product Name</th>
                            <th align="left">Transfer #</th>
                            <th align="center">Quantity</th>
                            <th align="left">From</th>
                            <th align="left">To</th>
                            <th align="left">Status</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td align="center">{{$loop->iteration}}.</td>
                            <td align="left">{{date('Y-m-d', strtotime($item->created_at))}}</td>
                            <td>
                                @if($item->currentStock && $item->currentStock->product)
                                    {{ $item->currentStock->product->name ?? '' }}
                                    {{ $item->currentStock->product->brand ?? '' }}
                                    {{ $item->currentStock->product->pack_size ?? '' }}
                                    {{ $item->currentStock->product->sales_uom ?? '' }}
                                @else
                                    Unkown
                                @endif
                                {{-- {{($item->currentStock['product']['name'] . ' ' ?? '') .
                                ($item->currentStock['product']['brand'] . ' ' ?? '') .
                                ($item->currentStock['product']['pack_size'] ?? '') .
                                $item->currentStock['product']['sales_uom'] ?? ''}} --}}
                            </td>
                            <td align="left">{{$item->transfer_no}}</td>
                            <td align="">{{number_format($item->transfer_qty)}}</td>
                            <td align="left">{{$item->fromStore['name']}}</td>
                            <td align="left">{{$item->toStore['name']}}</td>
                            <td align="left">
                                @php
                                    $status = ucfirst($item->status ?? '');
                                    $color = 'black';
                                    if ($status === 'Created') {
                                        $color = 'blue';
                                        $status = 'Pending';
                                    } elseif ($status === 'Completed') {
                                        $color = 'green';
                                    }
                                @endphp
                                <span style="color: {{$color}}; font-weight: bold;">{{$status}}</span>
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <script type="text/php">
    if ( isset($pdf) ) {
        $x = 400;
        $y = 560;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }


</script>

</body>

</html>