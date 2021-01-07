<!DOCTYPE html>
<html>
<head>
    <title>Stock Transfer</title>

    {{--    <link href="{{ asset('assets/apotek/css/pdf_table.css') }}" rel="stylesheet">--}}

    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
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
            border-collapse: collapse;
            width: 100%;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #receiver-sign {
            top: 10%;
        }

        #sender-sign {
            margin-top: 0%;
        }

        .topcorner {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: -4%;
            margin-left: 78%;
        }

        .topcorner > p {
            font-size: 10px;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }


        #table-detail-top {
            border-collapse: collapse;
            width: 50%;
            margin-top: 1%;
        }

        #table-detail-total {
            margin-top: 1%;
            width: 50%;
            margin-right: 20%;
        }
    </style>

</head>
<body>
<h1 align="center">{{$pharmacy['name']}}</h1>
<h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
<h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
<h3 align="center" style="margin-top: -1%">{{$pharmacy['email'].' | '.$pharmacy['website']}}</h3>
<h2 align="center" style="margin-top: -1%">Stock Transfer</h2>

{{--<div class="topcorner">--}}
{{--    <p>{{$transfer_detail['transfer_no']}}</p>--}}
{{--</div>--}}

<!-- DivTable.com -->

<div class="row">
    <div class="col-md-12">

        <table id="table-detail-top">
            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">
                <th>Transfer No</th>
                <td>{{$data[0]['transfer_no']}}</td>
            </tr>
            <tr style="background: white; color: black; font-size: 0.9em">
                <th>Date</th>
                <td>{{date('d-m-Y', strtotime($data[0]['created_at']))}}</td>
            </tr>
            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">
                <th>From</th>
                <td>{{$data[0]->fromStore['name']}}</td>
            </tr>
            <tr style="background: white; color: black; font-size: 0.9em">
                <th>To</th>
                <td>{{$data[0]->toStore['name']}}</td>
            </tr>
            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">
                <th>Remarks</th>
                <td>{{$data[0]->remarks}}</td>
            </tr>
        </table>

        <table id="table-detail" align="center">
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.9em">
                <th>Product Name</th>
                <th align="center">Quantity</th>
            </tr>
            </thead>
            <!-- loop the product names here -->
            @foreach($data as $transfer)
                <tr>
                    <td>{{ $transfer->currentStock['product']['name'] }}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{ number_format($transfer->transfer_qty) }}</div>
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

