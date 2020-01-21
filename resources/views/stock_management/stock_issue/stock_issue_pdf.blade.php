<!DOCTYPE html>
<html>
<head>
    <title>Stock Issue</title>

    <style>

        body {
            /*font-size: 30px;*/
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

        #table-detail-right {
            border-collapse: collapse;
            margin-top: 5%;
            margin-right: -50%;
        }

        #table-detail-total {
            margin-top: 1%;
            width: 50%;
            margin-right: 20%;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 14px;
            width: 50%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }


        #table-detail {
            border-spacing: 6%;
            width: 96%;
            margin-top: -13%;
            border: none;
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

<h4 align="center">{{$pharmacy['name']}}</h4>
<h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
<h2 align="center" style="margin-top: -2%">Stock Issue</h2>
<h5 align="center" style="margin-top: -2%">Phone: {{$pharmacy['phone']}}</h5>


{{--<div class="topcorner">--}}
{{--    <p>{{$data[0]['issue_no']}}</p>--}}
{{--</div>--}}

<div class="row">
    <div class="col-md-12">

        {{--        <table id="table-detail-top">--}}
        {{--            <tr style="background: #f2f2f2; color: black; font-size: 0.9em">--}}
        {{--                <th>Issue No</th>--}}
        {{--                <td>{{$data[0]['issue_no']}}</td>--}}
        {{--            </tr>--}}
        {{--            <tr style="background: white; color: black; font-size: 0.9em">--}}
        {{--                <th>Issue Date</th>--}}
        {{--                <td>{{date('d-m-Y', strtotime($data[0]['created_at']))}}</td>--}}
        {{--            </tr>--}}
        {{--        </table>--}}


        <div class="full-row" style="padding-top: 5%">
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Issued To: </b></div>
                    <div class="col-50" align="right">{{$data[0]->issueLocation['name']}}</div>
                </div>
            </div>
            <div class="col-50"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Issued By: </b></div>
                    <div class="col-50" align="right">{{$data[0]->user['name']}}</div>
                </div>
            </div>
        </div>
        <div class="full-row" style="margin-top: 1%; margin-bottom: 13%">
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Issue No:</b></div>
                    <div class="col-50" align="right">{{$data[0]['issue_no']}}</div>
                </div>
            </div>
            <div class="col-50"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Issued Date: </b></div>
                    <div class="col-50" align="right">{{date('d-m-Y',strtotime($data[0]['created_at']))}}</div>
                </div>
            </div>
        </div>


        <table id="table-detail" align="center">
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.9em">
                <th>Product Name</th>
                <th>Expiry Date</th>
                <th>Qty</th>
                <th align="right">Price</th>
                <th align="right">Amount</th>
            </tr>
            </thead>
            <!-- loop the product names here -->
            @foreach($data as $issue)
                <tr style="align-items: center">
                    <td>{{ $issue->currentStock['product']['name'] }}</td>
                    <td>{{ date('d-m-Y',strtotime($issue->currentStock['expiry_date'])) }}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{ floatval($issue->quantity) }}</div>
                    </td>
                    <td align="right">{{number_format($issue->currentStock['unit_cost'],2)}}</td>
                    <td align="right">{{number_format($issue->sub_total,2)}}</td>
                </tr>
            @endforeach

        </table>

        <div style="margin-left: 68%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total: </b>
        </div>
        <div align="right"
             style="margin-top: -10%; padding-top: 1%; padding-right: 2%">{{number_format(session('issue_total'),2)}}</div>


    </div>
</div>

</body>
</html>

