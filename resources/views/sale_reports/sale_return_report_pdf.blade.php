<!DOCTYPE html>
<html>
<head>
    <title>Sale Return Report</title>

    <style>

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
            /*border: 1px solid #FFFFFF;*/
            border-collapse: collapse;
        }

        #table-detail tr {
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
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            @endif
        </div>
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h4 align="center">{{$pharmacy['name']}}</h4>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
    <h2 align="center" style="margin-top: -2%">Sale Return Report</h2>
    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.7em">
                    <th>Product Name</th>
                    <th>Buy Date</th>
                    <th>Qty Bought</th>
                    <th>Return Date</th>
                    <th>Qty Returned</th>
                    <th>Reason</th>
                    <th>Refund</th>
                </tr>
                <thead>
                <tbody>
                @foreach($data as $datas)
                    <tr>
                        <td>{{$datas['item_returned']['name']}}</td>
                        <td>{{date('d-m-Y', strtotime($datas['item_returned']['b_date']))}}</td>
                        @if($datas['status']==5)
                            <td>{{$datas['item_returned']['bought_qty']+$datas['item_returned']['rtn_qty']}}</td>
                        @else
                            <td>{{$datas['item_returned']['bought_qty']}}</td>
                        @endif

                        <td>{{date('d-m-Y', strtotime($datas['date']))}}</td>
                        <td>{{$datas['item_returned']['rtn_qty']}}</td>
                        <td>{{$datas['reason']}}</td>
                        <td>{{number_format((($datas['item_returned']['rtn_qty'])/
                        ($datas['item_returned']['bought_qty']))*($datas['item_returned']['amount']
                        -$datas['item_returned']['discount']),2)}}</td>

                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>

