<!DOCTYPE html>
<html>

<head>
    <title>Credit Sales Details Report</title>
    <style>
        body {
            font-size: 14px;
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

        #table-detail-main {
            width: 103%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
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
    <div class="row">
        <div id="container">
            <div class="logo-container">
                @if($pharmacy['logo'])
                    <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
                @endif
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </h3>
        <h2 align="center" style="margin-top: -1%">Credit Sales Details Report</h2>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        @foreach($data as $datas => $dat)
            {{-- {{$pharmacy['tin_number']}} {{date('j M, Y', strtotime($dat[0]['created_at']))}}--}}
            <table id="table-detail-main">
                <tr>
                    <td><b>Receipt #:</b> {{$datas}}</td>
                    <td><b>Customer:</b> {{$dat[0]['customer']}}</td>
                    {{-- <td><b>Sold By:</b> {{$dat[0]['sold_by']}}</td> --}}
                </tr>
                <tr>
                    <td><b>Date:</b> {{date('j M, Y', strtotime($dat[0]['created_at']))}}</td>
                    <td><b>TIN #:</b> {{$pharmacy['tin_number']}}</td>
                </tr>
            </table>
            <table id="table-detail" align="center" style="margin-top: -1%; padding-top: 0%;">
                <!-- loop the product names here -->
                <thead>
                    <tr style="background: #1f273b; color: white;">
                        <th align="left" style="width: 1%;">#</th>
                        <th align="left">Product Name</th>
                        <th align="right">Batch #</th>
                        <th align="right">Sold By</th>
                        {{-- <th align="right">Date</th> --}}
                        <th align="right">Qty</th>
                        <th align="right">Sell Price</th>
                        <th align="right">Sub Total</th>
                        <th align="right">VAT</th>
                        <th align="right">Discount</th>
                        <th align="right">Amount</th>
                    </tr>

                    @foreach($dat as $item)
                        <tr>
                            <td align="left">{{$loop->iteration}}</td>
                            <td align="left">{{$item['name']}}</td>
                            <td align="left">{{$item['receipt_number']}}</td>
                            <td align="left">{{$item['sold_by']}}</td>
                            {{-- <td align="left">{{$item['date']}}</td> --}}
                            <td align="right">{{number_format($item['quantity'], 0)}}</td>
                            <td align="right">{{number_format($item['price'], 2)}}</td>
                            <td align="right">{{number_format($item['sub_total'], 2)}}</td>
                            <td align="right">{{number_format($item['vat'], 2)}}</td>
                            <td align="right">{{number_format($item['discount'], 2)}}</td>
                            <td align="right">{{number_format($item['amount'], 2)}}</td>
                        </tr>
                    @endforeach

            </table>
            <table style="width: 101%; margin-bottom: 5%;">
                <tr>
                    <td align="right" style="padding-top: -4%; width: 60%;"><b>Sub Total:</b></td>
                    <td align="right" style="padding-top: -4%;">
                        {{number_format(($dat[0]['grand_total'] - $dat[0]['total_vat']), 2)}}
                    </td>
                    <td align="right" style="padding-top: -4%; width: 20%;"><b>Paid:</b></td>
                    <td align="right" style="padding-top: -4%">{{number_format($dat[0]['paid'], 2)}}</td>
                </tr>
                <tr>
                    <td align="right" style="padding-top: -4%; width: 60%;"><b>VAT:</b></td>
                    <td align="right" style="padding-top: -4%">{{number_format($dat[0]['total_vat'], 2)}}</td>
                    <td align="right" style="padding-top: -4%; width: 20%;"><b>Balance:</b></td>
                    <td align="right" style="padding-top: -4%">{{number_format($dat[0]['balance'], 2)}}</td>
                </tr>
                @if ($enable_discount === 'YES')
                    <tr>
                        <td align="right" style="padding-top: -3%; width: 40%;"><b>Discount:</b></td>
                        <td align="right" style="padding-top: -3%">{{number_format($dat[0]['total_discount'], 2)}}</td>
                    </tr>
                @endif
                <tr>
                    <td align="right" style="padding-top: -3%; width: 40%;"><b>Total:</b></td>
                    <td align="right" style="padding-top: -3%">{{number_format($dat[0]['grand_total'], 2)}}</td>
                </tr>
            </table>
            <hr>
        @endforeach


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