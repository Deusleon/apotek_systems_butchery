<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>

        body {
            font-size: 10px;
        }

        * {
            font-family: MingLiu, MingLiU-ExtB, sans-serif;
        }

        table, th, td {
            /*border: 1px solid black;*/
            /*border-collapse: collapse;*/
            padding: 10px;
        }

        table {
            page-break-inside: auto;
            border-collapse: collapse;
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
            margin-top: -2%;
            border-top: 1px solid black;
        }

        #table-detail thead tr th {
            border-bottom: 1px solid #000000;
        }

        #table-detail-main {
            width: 103%;
            margin-top: -2%;
            margin-bottom: -2%;
            /*border-collapse: collapse;*/

        }

        #table-detail tr > {
            /*line-height: 13px;*/
        }

        #table-detail-main tr > {
            line-height: 15px;
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
{{--<div class="row" style="width: 50%">--}}
{{--    <div id="container">--}}
{{--        <div class="logo-container">--}}
{{--            @if($pharmacy['logo'])--}}
{{--                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="row" style="padding-top: -3%; width: 40%; margin-left: -6.5%">
    <h3 align="center"><b>RECEIPT</b></h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['name']}}</h3>
    <h5 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h5>
    @foreach($data as $datas => $dat)
        <table id="table-detail-main">
            <tr>
                <td>Receipt #: {{$datas}}</td>
            </tr>
            <tr>
                <td style="padding-top: -1%">Customer: {{$data[$datas][0]['customer']}}</td>
            </tr>
            <tr>
                <td style="padding-top: -1%">TIN: {{$pharmacy['tin_number']}}</td>
            </tr>
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr>
                <th align="left" style="width: 50%">Description</th>
                <th align="right">Qty</th>
                <th align="right" style="width: 25%">Amount</th>
            </tr>
            </thead>
            @foreach($dat as $item)
                <tr>
                    <td align="left">{{$item['name']}}</td>
                    <td align="right">{{$item['quantity']}}</td>
                    <td align="right">{{number_format($item['sub_total'],2)}}</td>
                </tr>
            @endforeach
        </table>
        <hr style="margin-left: 4%; margin-right: -4%">
        <table id="table-total" style="width: 100%;">
            <tr>
                <td style="padding-top: -1%; width: 50%">Total:</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="" style="padding-top: -1%">
                    <div style="margin-left: -15%; margin-right: 15%">{{number_format(($dat[0]['grand_total']),2)}}</div>
                </td>
            </tr>
        </table>
        <hr style="margin-left: 4%; margin-right: -4%; margin-top: -2%">

        <h5 align="center" style="margin-top: -0%">Sold By {{$dat[0]['sold_by']}}</h5>
    @endforeach

</div>

</body>

{{--<script type="text/javascript">--}}
{{--    try {--}}
{{--        this.print();--}}
{{--    } catch (e) {--}}
{{--        window.onload = window.print;--}}
{{--    }--}}
{{--</script>--}}

</html>

