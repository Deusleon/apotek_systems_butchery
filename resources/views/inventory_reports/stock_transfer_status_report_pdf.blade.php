<!DOCTYPE html>
<html>

<head>
    <title>Stock Transfer Status Report</title>

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
        th,
        td {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            padding: 10px;
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
            /*border-spacing: 5px;*/
            width: 100%;
            /*margin-top: -10%;*/
        }

        #table-detail-main {
            width: 102%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
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
    </style>

</head>

<body>

    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'] . ' | ' . $pharmacy['website']}}</h3>
        <h2 align="center" style="margin-top: -1%">Stock Transfer Status Report</h2>
        <h3 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d', strtotime($data[0]['from']))}}</b> To:
            <b>{{date('Y-m-d', strtotime($data[0]['to']))}}</b>
        </h3>
        <h3 align="center" style="margin-top: -1%">
            @if($data[0]->status == 'completed')
                Completed Transfers
            @else
                Pending Transfers
            @endif
        </h3>
        <div class="row">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <!-- loop the product names here -->
                    <thead>
                        <tr style="background: #1f273b; color: white;">
                            <th align="left">#</th>
                            <th align="left">Date</th>
                            <th align="left">Product Name</th>
                            <th align="left">Transfer #</th>
                            <th align="left">From</th>
                            <th align="left">To</th>
                            <th align="center">Transferred Qty</th>
                            <th align="center">Received Qty</th>
                        </tr>
                    </thead>
                    @foreach($data as $item)
                        <tr>
                            <td align="left">{{$loop->iteration}}.</td>
                            <td align="left">{{date('Y-m-d', strtotime($item->created_at))}}</td>
                            <td align="left">{{($item->currentStock['product']['name'] . ' ' ?? '') . ($item->currentStock['product']['brand'] . ' ' ?? '') . ($item->currentStock['product']['pack_size'] ?? '') . $item->currentStock['product']['sales_uom'] ?? ''}}</td>
                            <td align="left">{{$item->transfer_no}}</td>
                            <td align="left">{{$item->fromStore['name']}}</td>
                            <td align="left">{{$item->toStore['name']}}</td>
                            <td align="center">{{number_format($item->transfer_qty)}}</td>
                            <td align="center">{{number_format($item->accepted_qty)}}</td>
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