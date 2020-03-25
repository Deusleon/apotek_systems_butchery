<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>

    <style>

        body {
            /*font-size: 23px;*/
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
            /*margin-top: -10%;*/
        }

        #table-detail-main {
            width: 50%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
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
<h4 align="center" style="margin-top: -2%">{{$pharmacy['phone']}}</h4>

<h2 align="center" style="margin-top: -2%">Purchase Order</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail-main">
            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">
                <th>Order No</th>
                <td>{{$data[0]->order['order_number']}}</td>
            </tr>
            <tr style="background: white; color: black; font-size: 0.9em">
                <th>Order Date</th>
                <td>{{date('d-m-Y', strtotime($data[0]->order['ordered_at']))}}</td>
            </tr>
            {{--            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">--}}
            {{--                <th>Note</th>--}}
            {{--                <td>{{$data[0]->order['Comment']}}</td>--}}
            {{--            </tr>--}}
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white;">
                <th align="left">Product Name</th>
                <th align="center">Quantity</th>
                <th align="right">Price</th>
                <th align="right">VAT</th>
                <th align="right">Amount</th>
            </tr>
            </thead>
            @foreach($data as $item)
                <tr>
                    <td align="left">{{$item->product['name']}}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{$item->ordered_qty}}</div>
                    </td>
                    <td align="right">{{number_format($item->unit_price,2)}}</td>
                    <td align="right">{{number_format($item->vat,2)}}</td>
                    <td align="right">{{number_format($item->amount,2)}}</td>
                </tr>
            @endforeach
        </table>
        <hr>

        <div style="margin-left: 0%;width: 50%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Note: </b>
            <div align="left"
                 style="margin-top: 0%; width: 100%; padding-top: 0%; padding-left: 0%">
                <span>
                    {{$data[0]->order['Comment']}}
                </span>
            </div>
        </div>

        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: -10%; padding: 1%"><b>Sub Total: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->max('sub_totals'),2)}}</div>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>VAT: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->max('vats'),2)}}</div>
        <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total Amount: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-left: 1%">{{number_format($data->max('total'),2)}}</div>

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

