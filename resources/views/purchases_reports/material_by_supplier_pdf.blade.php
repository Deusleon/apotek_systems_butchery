<!DOCTYPE html>
<html>
<head>
    <title>Material Received Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            /*font-size: 30px;*/
        }

        table, th, td {
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
            margin-top: -10%;
        }

        #table-detail tr > {
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

<h4 align="center">{{$pharmacy['name']}}</h4>
<h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
<h2 align="center" style="margin-top: -2%">Material Received Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.9em">
                <th>Code</th>
                <th>Product Name</th>
                <th style="text-align: center">Quantity</th>
                <th style="text-align: center">Buy Price</th>
                <th style="text-align: center">Sell Price</th>
                <th style="text-align: center">Expire Date</th>
                <th style="text-align: center">Receive Date</th>

            </tr>
            </thead>
            @foreach($data as $item)
                <tr>
                    <td>{{$item->product['id']}}</td>
                    <td>{{$item->product['name']}}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{number_format($item->quantity,0)}}</div>
                    </td>
                    <td align="">{{number_format($item->unit_cost,2)}}</td>
                    <td align="">{{number_format($item->sell_price,2)}}</td>
                    <td align="">{{$item->expire_date}}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{date('Y-m-d',strtotime($item->created_at))}}</div>
                    </td>
                </tr>
            @endforeach
        </table>
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
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
     }


</script>

</body>
</html>

