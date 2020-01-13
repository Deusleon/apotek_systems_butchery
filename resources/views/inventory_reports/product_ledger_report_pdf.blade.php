<!DOCTYPE html>
<html>
<head>
    <title>Product Ledger Report</title>

    <style>

        body {
            /*font-size: 30px;*/
        }

        * {
            /*font-family: Verdana, Arial, sans-serif;*/
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

        tr:nth-child(even) {
            background-color: #f2f2f2;
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
            border: 1px solid #FFFFFF;
            border-collapse: collapse;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        #table-detail-main tr {
            line-height: 14px;
        }

        #table-detail tr {
            line-height: 14px;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }


    </style>

</head>
<body>

<h4 align="center">{{$pharmacy['name']}}</h4>
<h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
<h2 align="center" style="margin-top: -2%">Product Ledger Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail-main">
            <tr>
                <td style="background: #1f273b; color: white"><b>Product Name:</b> {{$data[0]['name']}}</td>
            </tr>
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            {{--            @foreach($data as $datas => $dat)--}}
            {{--                <tr>--}}
            {{--                    <td style="border: none" colspan="5"><b>Product Name:</b> {{$datas}}</td>--}}
            {{--                </tr>--}}
            <thead>
            <tr style="background: #1f273b; color: white">
                <th>Transaction Date</th>
                <th style="text-align: center">Transaction Method</th>
                <th style="text-align: center">Received</th>
                <th style="text-align: center">Outgoing</th>
                <th style="text-align: center">Balance</th>
            </tr>
            </thead>
            @foreach($data as $item)
                <tr>
                    <td>{{$item['date']}}</td>
                    <td>{{$item['method']}}</td>
                    <td style="text-align: right;">
                        <div style="margin-right: 50%">{{number_format($item['received'])}}</div>
                    </td>
                    <td style="text-align: right;">
                        <div style="margin-right: 50%">{{number_format($item['outgoing'])}}</div>
                    </td>
                    <td style="text-align: right;">{{number_format($item['balance'])}}</td>
                </tr>
            @endforeach
            {{--            @endforeach--}}
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

