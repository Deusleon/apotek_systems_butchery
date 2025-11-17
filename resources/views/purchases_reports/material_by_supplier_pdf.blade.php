<!DOCTYPE html>
<html>
<head>
    <title>Material Received Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th {
            border-collapse: collapse;
            padding: 8px;
        }

        table,
        td {
            border-collapse: collapse;
            padding: 5px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
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
            max-width: 160px;
            max-height: 160px;
        }
    </style>
</head>
<body>
<div class="row" style="padding-top: -2%">
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;"
                src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Material Received Report</h3>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="left" style="width: 1%;">#</th>
                    <th align="left">Product Name</th>
                    <th align="center">Quantity</th>
                    <th align="right">Buy Price</th>
                    <th align="right">Sell Price</th>
                    <th align="left">Expiry Date</th>
                    <th align="left">Receive Date</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td align="left">{{$loop->iteration}}.</td>
                        <td align="left">{{$item->product_name}}</td>
                        <td align="center">{{number_format($item->quantity,0)}}</td>
                        <td align="right">{{number_format($item->unit_cost,2)}}</td>
                        <td align="right">{{number_format($item->sell_price,2)}}</td>
                        <td align="left">{{$item->expire_date}}</td>
                        <td align="left">{{date('Y-m-d',strtotime($item->created_at))}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

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