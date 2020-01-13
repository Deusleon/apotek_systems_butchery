<!DOCTYPE html>
<html>
<head>
    <title>Cash Sale Summary Report</title>
    <style>

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 10px;
            font-size: x-small;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 13px;
            width: 50%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 35%;
        }

        .col-15 {
            display: inline-block;
            font-size: 13px;
            width: 15%;
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
    <h3 align="center">{{$pharmacy['name']}}</h3>
    <h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>
    <h4 align="center">Cash Sales Summary Report</h4>
    <h4 align="center" style="margin-top: -2%">{{$pharmacy['date_range']}}</h4>
    <div class="row" style="margin-top: 13%">
        <table id="table-detail" align="center">
            <tr>
                <th align="center">Sale Date</th>
                <th align="center">Sub Total</th>
                {{--                <th>Sold By</th>--}}
            </tr>

            @foreach($data as $item)
                <tr>
                    <td align="center">{{date('d-m-Y',strtotime($item['date']))}}</td>
                    <td align="right">
                        <div style="margin-right: 40%">{{number_format($item['sub_total'],2)}}</div>
                    </td>
                    {{--                    <td align="center">{{$item['sold_by']}}</td>--}}
                </tr>
            @endforeach
        </table>
    </div>

</body>
</html>


