<!DOCTYPE html>
<html>
<head>
    <title>PriceList Report</title>

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
    <h2 align="center" style="margin-top: -2%">Price List Report</h2>
    <h4 align="center" style="margin-top: -2%">For {{$data[0]['category_name']}} category</h4>
    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th>Product Name</th>
                    <th align="right">Buy Price</th>
                    <th align="right">Sell Price</th>
                </tr>
                </thead>

                @foreach($data as $item)
                    <tr>
                        <td>{{$item['name']}}</td>
                        <td align="right">{{number_format($item['buy_price'],2)}}</td>
                        <td align="right">{{number_format($item['sell_price'],2)}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>

</body>
</html>

